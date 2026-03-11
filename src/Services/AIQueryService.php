<?php

namespace Mrorko840\AiAnalytics\Services;

use Mrorko840\AiAnalytics\Contracts\AiProviderInterface;
use Mrorko840\AiAnalytics\Models\AiAnalyticsDataSource;
use Illuminate\Support\Facades\DB;
use Exception;

class AIQueryService
{
    protected AiProviderInterface $aiProvider;
    protected SchemaDiscoveryService $schemaService;
    protected QueryGuardService $queryGuard;
    protected ChatMemoryService $memoryService;

    public function __construct(
        AiProviderInterface $aiProvider,
        SchemaDiscoveryService $schemaService,
        QueryGuardService $queryGuard,
        ChatMemoryService $memoryService
    ) {
        $this->aiProvider = $aiProvider;
        $this->schemaService = $schemaService;
        $this->queryGuard = $queryGuard;
        $this->memoryService = $memoryService;
    }

    public function executePrompt(string $prompt, ?\Mrorko840\AiAnalytics\Models\AiAnalyticsChat $chat = null): array
    {
        $schemaContext = $this->buildSchemaContext();

        if (empty($schemaContext)) {
            return [
                'reply' => "I cannot answer this question because no tables have been enabled for analytics. Please enable data sources in the settings.",
                'data' => [],
                'sql' => null,
            ];
        }

        $systemPrompt = "You are a database analytics AI assistant. You must generate a highly efficient SQL query based on the user's prompt.\n";
        $systemPrompt .= "Use ONLY the following database schema:\n" . $schemaContext . "\n";
        
        // Inject defined Semantic Metrics so it inherently learns Business Language mapped to specific Cards directly
        $cards = \Mrorko840\AiAnalytics\Models\AiAnalyticsCard::all();
        if ($cards->count() > 0) {
            $systemPrompt .= "\nBUSINESS DEFINITIONS/CONTEXT YOU MUST LEARN:\n";
            foreach ($cards as $card) {
                if (is_array($card->filters) && count($card->filters) > 0) {
                    $systemPrompt .= "- When the user mentions '{$card->name}', this is defined internally as querying table '{$card->table_name}' with the following condition(s): ";
                    foreach ($card->filters as $f) {
                        $col = $f['column'] ?? '';
                        $op = $f['operator'] ?? '=';
                        $val = $f['value'] ?? '';
                        $systemPrompt .= "{$col} {$op} {$val} AND ";
                    }
                    $systemPrompt = rtrim($systemPrompt, " AND ") . ".\n";
                }
            }
        }

        $systemPrompt .= "\nIMPORTANT RULES:\n";
        $systemPrompt .= "1. Respond ONLY with the raw SQL query. No markdown formatting, no comments, no explanations.\n";
        $systemPrompt .= "2. The query MUST start with SELECT and must NOT contain any destructive operations.\n";
        $systemPrompt .= "3. Format dates properly depending on the usual SQL standards.\n";
        
        try {
            // Include conversational memory arrays
            $messagesPayload = [];
            if ($chat) {
                // If we have a chat ID, the prompt logic relies on AI interface supporting history arrays
                // Assuming AiProvider->ask supports a 'context' block or similar, we serialize past messages.
                // For a robust implementation we stringify the past context explicitly if specific roles aren't supported uniformly yet.
                $historyStrings = [];
                $recentContext = $this->memoryService->getRecentHistory($chat);
                foreach ($recentContext as $msg) {
                    $historyStrings[] = ($msg['role'] === 'user' ? 'User:' : 'AI:') . ' ' . $msg['content'];
                }
                
                if (!empty($historyStrings)) {
                    $systemPrompt .= "\nPREVIOUS CONVERSATIONAL MEMORY TO HELP YOU UNDERSTAND CONTEXT ('It', 'That', etc):\n" . implode("\n", $historyStrings) . "\n";
                }
            }

            // 1. Get SQL from AI
            $sql = $this->aiProvider->ask($prompt, [$systemPrompt]);
            
            // Clean up possible markdown code blocks
            $sql = trim(preg_replace('/^```sql|```$/i', '', $sql));
            
            // 2. Validate SQL
            if (!$this->queryGuard->isSafe($sql)) {
                return [
                    'reply' => "I apologize, but the generated query was blocked by the safety guard because it contained restricted or non-SELECT operations.",
                    'data' => [],
                    'sql' => $sql,
                ];
            }

            // 3. Execute SQL
            $connectionName = config('ai-analytics.database_connection');
            $results = DB::connection($connectionName)->select($sql);
            
            // Limit results to prevent context overflow in summary
            $resultsArray = array_map(function($item) { return (array) $item; }, $results);
            $summaryData = array_slice($resultsArray, 0, 50);

            // 4. Get Summary from AI
            $summarySystemPrompt = "You are a helpful data analyst. You have been given a user's question and the raw data results from the database. Provide a concise, professional, and friendly answer/summary based ONLY on the data provided. Never mention the SQL query itself to the user.";
            $summaryPrompt = "User's Question: " . $prompt . "\n\nData Results:\n" . json_encode($summaryData);
            
            $summary = $this->aiProvider->ask($summaryPrompt, [$summarySystemPrompt]);

            $isReportIntent = preg_match('/(generate|create|banao|dao|make|show|export).*report/i', $prompt);
            $reportId = null;
            
            if ($isReportIntent) {
                $reportTitle = "Report: " . substr(ucfirst(preg_replace('/[^a-zA-Z0-9 ]/m', '', $prompt)), 0, 50);
                
                $payloadData = [
                    'id' => uniqid(),
                    'title' => $reportTitle,
                    'subtitle' => 'Chat Generated Analytics Report',
                    'period' => 'Custom Chat Context',
                    'insights' => $summary,
                    'raw_query' => $sql,
                    'tabular_data' => array_slice($resultsArray, 0, 100), // Cap report data length to 100 max
                    'metadata' => [
                        'generated_at' => now()->toDateTimeString(),
                    ]
                ];

                $report = \Mrorko840\AiAnalytics\Models\AiAnalyticsReport::create([
                    'user_id' => auth()->id() ?? 0,
                    'title' => $reportTitle,
                    'report_type' => 'chat_generated',
                    'filters' => [],
                    'payload' => $payloadData,
                ]);
                
                $reportId = $report->id;
            }

            return [
                'reply' => $summary,
                'data' => $resultsArray,
                'sql' => $sql,
                'report_id' => $reportId,
            ];

        } catch (Exception $e) {
            report($e);
            return [
                'reply' => "An error occurred while trying to process the data request: " . $e->getMessage(),
                'data' => [],
                'sql' => $sql ?? null,
            ];
        }
    }

    protected function buildSchemaContext(): string
    {
        $enabledTables = AiAnalyticsDataSource::where('is_enabled', true)->pluck('table_name')->toArray();
        if (empty($enabledTables)) {
            return '';
        }

        $context = [];
        foreach ($enabledTables as $table) {
            $columns = $this->schemaService->getTableColumns($table);
            $context[] = $table . "(" . implode(', ', $columns) . ")";
        }

        return "Tables: \n" . implode("\n", $context);
    }
}
