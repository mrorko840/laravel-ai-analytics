<?php

namespace Mrorko840\AiAnalytics\Services;

use Carbon\Carbon;

class TransactionAnalyticsService
{
    private EntityMappingResolver $resolver;

    public function __construct(EntityMappingResolver $resolver)
    {
        $this->resolver = $resolver;
    }

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
        $resolved = $this->resolver->resolveEntity('transaction');
        
        if (!$resolved) {
            throw new \Exception("Transaction entity mapping is missing.");
        }

        $createdAtColumn = $resolved['mapping']['created_at_column'] ?? 'created_at';
        $amountColumn = $resolved['mapping']['amount_column'] ?? 'amount';
        $typeColumn = $resolved['mapping']['type_column'] ?? 'type';
        $statusColumn = $resolved['mapping']['status_column'] ?? 'status';
        
        $typeValue = $resolved['mapping'][$typeConfigKey] ?? null;
        
        $successStatuses = $resolved['mapping']['success_statuses'] ?? [];
        if (is_string($successStatuses)) {
            $successStatuses = array_map('trim', explode(',', $successStatuses));
        }

        $query = $this->resolver->getQueryBuilder('transaction')
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
