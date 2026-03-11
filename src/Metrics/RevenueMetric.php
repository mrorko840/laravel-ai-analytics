<?php

namespace Mrorko840\AiAnalytics\Metrics;

use Carbon\Carbon;
use Mrorko840\AiAnalytics\Services\RevenueAnalyticsService;

class RevenueMetric extends AbstractMetric
{
    protected RevenueAnalyticsService $service;

    public function __construct(RevenueAnalyticsService $service)
    {
        $this->service = $service;
    }

    public function name(): string
    {
        return 'revenue';
    }

    public function label(): string
    {
        return 'Total Revenue';
    }

    public function description(): string
    {
        return 'Calculates the total revenue from completed orders over a period of time.';
    }

    public function supportedFilters(): array
    {
        return ['from_date', 'to_date'];
    }

    public function execute(array $filters = []): mixed
    {
        $from = isset($filters['from_date']) ? Carbon::parse($filters['from_date']) : now()->subDays(30);
        $to = isset($filters['to_date']) ? Carbon::parse($filters['to_date']) : now();

        return [
            'value' => $this->service->getRevenue($from, $to),
            'from' => $from->toDateTimeString(),
            'to' => $to->toDateTimeString()
        ];
    }
}
