<?php

namespace Mrorko840\AiAnalytics\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductAnalyticsService
{
    private EntityMappingResolver $resolver;

    public function __construct(EntityMappingResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getTopSellingProducts(Carbon $from, Carbon $to, int $limit = 10): array
    {
        $productResolved = $this->resolver->resolveEntity('product');
        $orderResolved = $this->resolver->resolveEntity('order');
        $orderItemResolved = $this->resolver->resolveEntity('order_item');
        
        if (!$productResolved || !$orderResolved || !$orderItemResolved) {
            throw new \Exception("Mappings for Product, Order, and Order Item are required for this metric.");
        }
        
        $productsTable = $productResolved['table'] ?? throw new \Exception("Product table mapping missing.");
        $ordersTable = $orderResolved['table'] ?? throw new \Exception("Order table mapping missing.");
        $orderItemsTable = $orderItemResolved['table'] ?? throw new \Exception("Order item table mapping missing.");

        $productIdCol = $productResolved['mapping']['id_column'] ?? 'id';
        $productNameCol = $productResolved['mapping']['name_column'] ?? 'name';
        
        $orderIdCol = $orderResolved['mapping']['id_column'] ?? 'id';
        $orderCreatedAt = $orderResolved['mapping']['created_at_column'] ?? 'created_at';
        $orderStatusCol = $orderResolved['mapping']['status_column'] ?? 'status';
        $paidStatuses = $orderResolved['mapping']['paid_statuses'] ?? [];
        if (is_string($paidStatuses)) {
            $paidStatuses = array_map('trim', explode(',', $paidStatuses));
        }
        
        $itemOrderIdFK = $orderItemResolved['mapping']['order_foreign_key'] ?? 'order_id';
        $itemProductIdFK = $orderItemResolved['mapping']['product_foreign_key'] ?? 'product_id';
        $itemQuantityCol = $orderItemResolved['mapping']['quantity_column'] ?? 'quantity';
        $itemPriceCol = $orderItemResolved['mapping']['price_column'] ?? 'price';

        // Example query: SELECT products.id, products.name, SUM(order_items.quantity) as sales, SUM(order_items.quantity * order_items.price) as revenue ...
        
        $query = DB::table($orderItemsTable)
            ->join($ordersTable, "{$ordersTable}.{$orderIdCol}", '=', "{$orderItemsTable}.{$itemOrderIdFK}")
            ->join($productsTable, "{$productsTable}.{$productIdCol}", '=', "{$orderItemsTable}.{$itemProductIdFK}")
            ->select(
                "{$productsTable}.{$productIdCol} as id",
                "{$productsTable}.{$productNameCol} as name",
                DB::raw("SUM({$orderItemsTable}.{$itemQuantityCol}) as sales"),
                DB::raw("CAST(SUM({$orderItemsTable}.{$itemQuantityCol} * {$orderItemsTable}.{$itemPriceCol}) as DECIMAL(10, 2)) as revenue")
            )
            ->whereBetween("{$ordersTable}.{$orderCreatedAt}", [$from, $to])
            ->groupBy("{$productsTable}.{$productIdCol}", "{$productsTable}.{$productNameCol}")
            ->orderBy('sales', 'desc')
            ->limit($limit);

        if (!empty($orderStatusCol) && !empty($paidStatuses)) {
            $query->whereIn("{$ordersTable}.{$orderStatusCol}", $paidStatuses);
        }

        // Return array of arrays
        return array_map(function($row) {
            return (array) $row;
        }, $query->get()->toArray());
    }
    
    public function getConversionRate(Carbon $from, Carbon $to): float
    {
        $userResolved = $this->resolver->resolveEntity('user');
        $orderResolved = $this->resolver->resolveEntity('order');
        
        if (!$userResolved || !$orderResolved) {
            throw new \Exception("Mappings for User and Order are required for Conversion tracking.");
        }

        $userCreatedAt = $userResolved['mapping']['created_at_column'] ?? 'created_at';
        $orderCreatedAt = $orderResolved['mapping']['created_at_column'] ?? 'created_at';
        $orderUserFK = $orderResolved['mapping']['user_foreign_key'] ?? 'user_id';
        
        $totalUsers = $this->resolver->getQueryBuilder('user')
            ->whereBetween($userCreatedAt, [$from, $to])
            ->count();
            
        if ($totalUsers === 0) return 0.0;
        
        $usersWithOrders = $this->resolver->getQueryBuilder('order')
            ->whereBetween($orderCreatedAt, [$from, $to])
            ->distinct()
            ->count($orderUserFK);
            
        return round(($usersWithOrders / $totalUsers) * 100, 2);
    }
}
