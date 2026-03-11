<?php

namespace Mrorko840\AiAnalytics\Metrics;

use Mrorko840\AiAnalytics\Contracts\MetricInterface;

abstract class AbstractMetric implements MetricInterface
{
    // Common helper methods can be placed here

    protected function getEntityConfig(string $entityName): array
    {
        return config("ai-analytics.entities.{$entityName}", []);
    }
}
