<?php

namespace Mrorko840\AiAnalytics\Metrics;

use Carbon\Carbon;
use Mrorko840\AiAnalytics\Services\UserAnalyticsService;

class SignupsCountMetric extends AbstractMetric
{
    protected UserAnalyticsService $service;

    public function __construct(UserAnalyticsService $service)
    {
        $this->service = $service;
    }

    public function name(): string
    {
        return 'signups_count';
    }

    public function label(): string
    {
        return 'Total Signups';
    }

    public function description(): string
    {
        return 'Counts the number of new user registrations over a period of time.';
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
            'value' => $this->service->getSignupsCount($from, $to),
            'from' => $from->toDateTimeString(),
            'to' => $to->toDateTimeString()
        ];
    }
}
