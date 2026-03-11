<?php

namespace Mrorko840\AiAnalytics\Services;

use Mrorko840\AiAnalytics\Contracts\MetricInterface;
use InvalidArgumentException;

class MetricRegistry
{
    /**
     * @var array<string, MetricInterface>
     */
    protected array $metrics = [];

    /**
     * Register a new metric.
     *
     * @param string $name
     * @param MetricInterface $metric
     * @return void
     */
    public function register(string $name, MetricInterface $metric): void
    {
        $this->metrics[$name] = $metric;
    }

    /**
     * Get a metric by name.
     *
     * @param string $name
     * @return MetricInterface
     */
    public function get(string $name): MetricInterface
    {
        if (!isset($this->metrics[$name])) {
            throw new InvalidArgumentException("Metric [{$name}] not found in registry.");
        }

        return $this->metrics[$name];
    }

    /**
     * Get all registered metrics.
     *
     * @return array<string, MetricInterface>
     */
    public function all(): array
    {
        return $this->metrics;
    }

    /**
     * Get detailed information about all registered metrics.
     * Useful for AI context.
     *
     * @return array
     */
    public function getAvailableMetricsInfo(): array
    {
        $info = [];
        foreach ($this->metrics as $name => $metric) {
            $info[] = [
                'name' => $metric->name(),
                'label' => $metric->label(),
                'description' => $metric->description(),
                'supported_filters' => $metric->supportedFilters(),
            ];
        }
        return $info;
    }
}
