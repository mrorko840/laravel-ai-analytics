<?php

namespace Mrorko840\AiAnalytics\Chat;

use Mrorko840\AiAnalytics\Services\MetricRegistry;
use Exception;

class MetricResolver
{
    protected MetricRegistry $registry;

    public function __construct(MetricRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Safely resolve and execute metrics based on intent.
     */
    public function resolveAndExecute(array $intent): array
    {
        $metricNames = $intent['metric_names'] ?? [];
        $filters = $intent['filters'] ?? [];
        
        $results = [];
        
        foreach ($metricNames as $name) {
            try {
                $metric = $this->registry->get($name);
                $results[$name] = [
                    'label' => $metric->label(),
                    'data' => $metric->execute($filters)
                ];
            } catch (Exception $e) {
                // Log exception gracefully instead of crashing
                report($e);
                
                $errorMsg = $e->getMessage();
                if (!config('app.debug')) {
                    $errorMsg = "Metric encountered a configuration or data mapping error.";
                }

                $results[$name] = [
                    'error' => "Metric failure: " . $errorMsg,
                ];
            }
        }
        
        return $results;
    }
}
