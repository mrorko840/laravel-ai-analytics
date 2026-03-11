<?php

namespace Mrorko840\AiAnalytics\Chat;

use Illuminate\Support\Facades\Http;
use Mrorko840\AiAnalytics\Contracts\AiProviderInterface;
use Exception;

class OpenAiProvider implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint = 'https://api.openai.com/v1/chat/completions';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->model = config('ai-analytics.ai.model', 'gpt-4o');
    }

    public function ask(string $prompt, array $systemMessages = []): string
    {
        $messages = [];

        foreach ($systemMessages as $msg) {
            $messages[] = ['role' => 'system', 'content' => $msg];
        }

        $messages[] = ['role' => 'user', 'content' => $prompt];

        $response = Http::withToken($this->apiKey)
            ->post($this->endpoint, [
                'model' => $this->model,
                'messages' => $messages,
            ]);

        if ($response->failed()) {
            throw new Exception('OpenAI API request failed: ' . $response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

    public function parseIntent(string $input, array $context = []): array
    {
        $systemMessage = "You are a specialized AI intent parser for an analytics system.\n";
        $systemMessage .= "Given a user's question, analyze it against the provided available metrics.\n";
        $systemMessage .= "Respond strictly in valid JSON format with parameters: 'metric_names' (array of strings), 'filters' (object with from_date, to_date, limit), 'summary_request' (boolean), and 'intent_type' (question, report, export).\n";

        $prompt = "Available Metrics: " . json_encode($context['metrics_info'] ?? []) . "\n";
        $prompt .= "User Question: " . $input . "\n";

        $response = Http::withToken($this->apiKey)
            ->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

        if ($response->failed()) {
            throw new Exception('OpenAI API request failed: ' . $response->body());
        }

        $jsonStr = $response->json('choices.0.message.content', '{}');
        return json_decode($jsonStr, true) ?? [];
    }
}
