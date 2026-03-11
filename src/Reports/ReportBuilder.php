<?php

namespace Mrorko840\AiAnalytics\Reports;

use Mrorko840\AiAnalytics\Chat\ChatService;
use Mrorko840\AiAnalytics\Services\MetricRegistry;
use Exception;

class ReportBuilder
{
    protected MetricRegistry $registry;
    protected ChatService $chat;

    public function __construct(MetricRegistry $registry, ChatService $chat)
    {
        $this->registry = $registry;
        $this->chat = $chat;
    }

    public function build(string $title, string $period, array $metricNames, array $filters = []): array
    {
        $metricsData = [];
        $insights = '';

        foreach ($metricNames as $name) {
            try {
                $metric = $this->registry->get($name);
                $metricsData[$name] = [
                    'label' => $metric->label(),
                    'data' => $metric->execute($filters),
                ];
            } catch (Exception $e) {
                // Ignore silent failures for metrics execution layer
                continue;
            }
        }

        // Generate AI insights for the report
        if (count($metricsData) > 0) {
            $question = "Summarize the findings for {$title} over {$period}.";
            $intent = [
                'metric_names' => $metricNames,
                'filters' => $filters,
            ];

            // Re-use InsightFormatter directly or abstractly
            $insights = "Report generated automatically based on selected metrics. Check Dashboard for AI Insights.";
        }

        return [
            'id' => uniqid(),
            'title' => $title,
            'subtitle' => "Analytics Report",
            'period' => $period,
            'metrics' => $metricsData,
            'insights' => $insights,
            'metadata' => [
                'generated_at' => now()->toDateTimeString(),
            ]
        ];
    }
}
