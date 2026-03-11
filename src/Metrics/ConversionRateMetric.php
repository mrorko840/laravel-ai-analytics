<?php

namespace Mrorko840\AiAnalytics\Metrics;

use Carbon\Carbon;
use Mrorko840\AiAnalytics\Services\ProductAnalyticsService;

class ConversionRateMetric extends AbstractMetric
{
    protected ProductAnalyticsService $service;

    public function __construct(ProductAnalyticsService $service)
    {
        $this->service = $service;
    }

    public function name(): string
    {
        return 'conversion_rate';
    }

    public function label(): string
    {
        return 'User Conversion Rate';
    }

    public function description(): string
    {
        return 'Calculates the percentage of users who made a purchase over a period of time.';
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
            'value' => $this->service->getConversionRate($from, $to),
            'from' => $from->toDateTimeString(),
            'to' => $to->toDateTimeString()
        ];
    }
}
