<?php

namespace Mrorko840\AiAnalytics\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueAnalyticsService
{
    public function getRevenue(Carbon $from, Carbon $to): float
    {
        $config = config('ai-analytics.entities.order');
        $modelClass = $config['model'] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return 0.0;
        }

        $createdAtColumn = $config['created_at_column'] ?? 'created_at';
        $amountColumn = $config['amount_column'] ?? 'total';
        $statusColumn = $config['status_column'] ?? 'status';
        $paidStatuses = $config['paid_statuses'] ?? [];

        $query = $modelClass::query()
            ->whereBetween($createdAtColumn, [$from, $to]);

        if (!empty($statusColumn) && !empty($paidStatuses)) {
            $query->whereIn($statusColumn, $paidStatuses);
        }

        return (float) $query->sum($amountColumn);
    }
}
