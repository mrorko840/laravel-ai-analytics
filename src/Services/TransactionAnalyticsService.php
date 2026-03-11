<?php

namespace Mrorko840\AiAnalytics\Services;

use Carbon\Carbon;

class TransactionAnalyticsService
{
    public function getWithdrawals(Carbon $from, Carbon $to): float
    {
        return $this->sumTransactionsByType('withdrawal_value', $from, $to);
    }

    public function getDeposits(Carbon $from, Carbon $to): float
    {
        return $this->sumTransactionsByType('deposit_value', $from, $to);
    }

    protected function sumTransactionsByType(string $typeConfigKey, Carbon $from, Carbon $to): float
    {
        $config = config('ai-analytics.entities.transaction');
        $modelClass = $config['model'] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return 0.0;
        }

        $createdAtColumn = $config['created_at_column'] ?? 'created_at';
        $amountColumn = $config['amount_column'] ?? 'amount';
        $typeColumn = $config['type_column'] ?? 'type';
        $statusColumn = $config['status_column'] ?? 'status';

        $typeValue = $config[$typeConfigKey] ?? null;
        $successStatuses = $config['success_statuses'] ?? [];

        $query = $modelClass::query()
            ->whereBetween($createdAtColumn, [$from, $to]);

        if ($typeValue) {
            $query->where($typeColumn, $typeValue);
        }

        if (!empty($statusColumn) && !empty($successStatuses)) {
            $query->whereIn($statusColumn, $successStatuses);
        }

        return (float) $query->sum($amountColumn);
    }
}
