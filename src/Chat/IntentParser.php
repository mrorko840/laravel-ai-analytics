<?php

namespace Mrorko840\AiAnalytics\Chat;

use Mrorko840\AiAnalytics\Contracts\AiProviderInterface;
use Mrorko840\AiAnalytics\Services\MetricRegistry;

class IntentParser
{
    protected AiProviderInterface $ai;
    protected MetricRegistry $registry;

    public function __construct(AiProviderInterface $ai, MetricRegistry $registry)
    {
        $this->ai = $ai;
        $this->registry = $registry;
    }

    public function parse(string $userInput): array
    {
        $context = [
            'metrics_info' => $this->registry->getAvailableMetricsInfo()
        ];

        $intentData = $this->ai->parseIntent($userInput, $context);

        // Ensure defaults
        return [
            'metric_names' => $intentData['metric_names'] ?? [],
            'filters' => $intentData['filters'] ?? [],
            'summary_request' => $intentData['summary_request'] ?? true,
            'intent_type' => $intentData['intent_type'] ?? 'question',
        ];
    }
}
