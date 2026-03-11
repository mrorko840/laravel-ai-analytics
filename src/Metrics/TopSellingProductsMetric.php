<?php

namespace Mrorko840\AiAnalytics\Metrics;

use Carbon\Carbon;
use Mrorko840\AiAnalytics\Services\ProductAnalyticsService;

class TopSellingProductsMetric extends AbstractMetric
{
    protected ProductAnalyticsService $service;

    public function __construct(ProductAnalyticsService $service)
    {
        $this->service = $service;
    }

    public function name(): string
    {
        return 'top_selling_products';
    }

    public function label(): string
    {
        return 'Top Selling Products';
    }

    public function description(): string
    {
        return 'Retrieves a list of the top selling products based on sales volume or revenue.';
    }

    public function supportedFilters(): array
    {
        return ['from_date', 'to_date', 'limit'];
    }

    public function execute(array $filters = []): mixed
    {
        $from = isset($filters['from_date']) ? Carbon::parse($filters['from_date']) : now()->subDays(30);
        $to = isset($filters['to_date']) ? Carbon::parse($filters['to_date']) : now();
        $limit = isset($filters['limit']) ? (int) $filters['limit'] : 10;

        return [
            'value' => $this->service->getTopSellingProducts($from, $to, $limit),
            'from' => $from->toDateTimeString(),
            'to' => $to->toDateTimeString(),
            'limit' => $limit
        ];
    }
}
