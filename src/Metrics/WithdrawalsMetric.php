<?php

namespace Mrorko840\AiAnalytics\Metrics;

use Carbon\Carbon;
use Mrorko840\AiAnalytics\Services\TransactionAnalyticsService;

class WithdrawalsMetric extends AbstractMetric
{
    protected TransactionAnalyticsService $service;

    public function __construct(TransactionAnalyticsService $service)
    {
        $this->service = $service;
    }

    public function name(): string
    {
        return 'withdrawals';
    }

    public function label(): string
    {
        return 'Total Withdrawals';
    }

    public function description(): string
    {
        return 'Calculates the total completed withdrawal amounts over a period of time.';
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
            'value' => $this->service->getWithdrawals($from, $to),
            'from' => $from->toDateTimeString(),
            'to' => $to->toDateTimeString()
        ];
    }
}
