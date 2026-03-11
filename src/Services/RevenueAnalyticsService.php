<?php

namespace Mrorko840\AiAnalytics\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueAnalyticsService
{
    private EntityMappingResolver $resolver;

    public function __construct(EntityMappingResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getRevenue(Carbon $from, Carbon $to): float
    {
        $resolved = $this->resolver->resolveEntity('order');
        
        if (!$resolved) {
            throw new \Exception("Order entity mapping is missing.");
        }

        $createdAtColumn = $resolved['mapping']['created_at_column'] ?? 'created_at';
        $amountColumn = $resolved['mapping']['amount_column'] ?? 'total';
        $statusColumn = $resolved['mapping']['status_column'] ?? 'status';
        
        $paidStatuses = $resolved['mapping']['paid_statuses'] ?? [];
        if (is_string($paidStatuses)) {
            $paidStatuses = array_map('trim', explode(',', $paidStatuses));
        }

        $query = $this->resolver->getQueryBuilder('order')
            ->whereBetween($createdAtColumn, [$from, $to]);

        if (!empty($statusColumn) && !empty($paidStatuses)) {
            $query->whereIn($statusColumn, $paidStatuses);
        }

        return (float) $query->sum($amountColumn);
    }
}
