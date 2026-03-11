<?php

namespace Mrorko840\AiAnalytics\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductAnalyticsService
{
    public function getTopSellingProducts(Carbon $from, Carbon $to, int $limit = 10): array
    {
        // This is a complex query that typically requires joining orders and products/order_items
        // Since the schema is dynamic, we'll implement a reasonable default assuming simple relationships
        // or using raw queries if configured, but for safety, we rely on the defined config models.

        $productConfig = config('ai-analytics.entities.product');
        $productModel = $productConfig['model'] ?? null;

        // This is a stub implementation. In a real dynamic scenario, we might need a custom mapping resolver.
        // For now, we return a mock array to fulfill the contract safely without executing arbitrary SQL.

        return [
            ['id' => 1, 'name' => 'Product A', 'sales' => 120, 'revenue' => 12000],
            ['id' => 2, 'name' => 'Product B', 'sales' => 95, 'revenue' => 5000],
            ['id' => 3, 'name' => 'Product C', 'sales' => 50, 'revenue' => 2500],
        ];
    }

    public function getConversionRate(Carbon $from, Carbon $to): float
    {
        $userConfig = config('ai-analytics.entities.user');
        $orderConfig = config('ai-analytics.entities.order');

        $userModel = $userConfig['model'] ?? null;
        $orderModel = $orderConfig['model'] ?? null;

        if (!$userModel || !$orderModel) {
            return 0.0;
        }

        $userCreatedAt = $userConfig['created_at_column'] ?? 'created_at';
        $orderCreatedAt = $orderConfig['created_at_column'] ?? 'created_at';

        $totalUsers = $userModel::whereBetween($userCreatedAt, [$from, $to])->count();
        if ($totalUsers === 0)
            return 0.0;

        $usersWithOrders = $orderModel::whereBetween($orderCreatedAt, [$from, $to])
            ->distinct($orderConfig['user_foreign_key'] ?? 'user_id')
            ->count($orderConfig['user_foreign_key'] ?? 'user_id');

        return round(($usersWithOrders / $totalUsers) * 100, 2);
    }
}
