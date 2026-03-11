<?php

namespace Mrorko840\AiAnalytics\Contracts;

interface MetricInterface
{
    /**
     * Get the internal machine name of the metric.
     */
    public function name(): string;

    /**
     * Get the human readable label.
     */
    public function label(): string;

    /**
     * Get a short description.
     */
    public function description(): string;

    /**
     * Get an array of supported filter keys (e.g. ['from_date', 'to_date', 'status']).
     */
    public function supportedFilters(): array;

    /**
     * Execute the metric query and return the result.
     * 
     * @param array $filters
     * @return mixed
     */
    public function execute(array $filters = []): mixed;
}
