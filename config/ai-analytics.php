<?php

return [
    'enabled' => env('AI_ANALYTICS_ENABLED', true),

    'route_prefix' => env('AI_ANALYTICS_ROUTE_PREFIX', 'ai-analytics'),

    'middleware' => ['web', 'auth'],

    'api_middleware' => ['api', 'auth:sanctum'],

    'database_connection' => env('AI_ANALYTICS_DB_CONNECTION', config('database.default')),

    'cache' => [
        'enabled' => env('AI_ANALYTICS_CACHE_ENABLED', true),
        'ttl' => env('AI_ANALYTICS_CACHE_TTL', 3600), // in seconds
    ],

    'queue' => [
        'connection' => env('AI_ANALYTICS_QUEUE_CONNECTION', 'default'),
        'queue' => env('AI_ANALYTICS_QUEUE_NAME', 'default'),
    ],

    'ai' => [
        'provider' => env('AI_ANALYTICS_PROVIDER', 'openai'),
        'api_key' => env('AI_ANALYTICS_API_KEY', ''),
        'model' => env('AI_ANALYTICS_MODEL', 'gpt-4o'),
    ],

    'exports' => [
        'pdf_driver' => 'dompdf',
        'csv_delimiter' => ',',
    ],

    'ui' => [
        'theme' => env('AI_ANALYTICS_THEME', 'light'),
    ],

    'tracking' => [
        'enabled' => true,
        'queue_events' => env('AI_ANALYTICS_QUEUE_EVENTS', false),
    ],

    'entities' => [
        'user' => [
            'model' => 'App\Models\User',
            'created_at_column' => 'created_at',
            'id_column' => 'id',
        ],

        'order' => [
            'model' => 'App\Models\Order',
            'created_at_column' => 'created_at',
            'amount_column' => 'total',
            'status_column' => 'status',
            'paid_statuses' => ['paid', 'completed'],
            'user_foreign_key' => 'user_id',
        ],

        'product' => [
            'model' => 'App\Models\Product',
            'id_column' => 'id',
            'name_column' => 'name',
            'price_column' => 'price',
        ],

        'transaction' => [
            'model' => 'App\Models\Transaction',
            'created_at_column' => 'created_at',
            'amount_column' => 'amount',
            'type_column' => 'type',
            'deposit_value' => 'deposit',
            'withdrawal_value' => 'withdraw',
            'status_column' => 'status',
            'success_statuses' => ['approved', 'completed', 'success'],
            'user_foreign_key' => 'user_id',
        ],

        'visit' => [
            'table' => 'page_visits',
            'created_at_column' => 'created_at',
            'user_foreign_key' => 'user_id',
            'url_column' => 'url',
        ],

        'product_view' => [
            'table' => 'product_views',
            'created_at_column' => 'created_at',
            'product_foreign_key' => 'product_id',
            'user_foreign_key' => 'user_id',
        ],
    ],

    'metrics' => [
        'signups_count' => Mrorko840\AiAnalytics\Metrics\SignupsCountMetric::class,
        'revenue' => Mrorko840\AiAnalytics\Metrics\RevenueMetric::class,
        'withdrawals' => Mrorko840\AiAnalytics\Metrics\WithdrawalsMetric::class,
        'deposits' => Mrorko840\AiAnalytics\Metrics\DepositsMetric::class,
        'conversion_rate' => Mrorko840\AiAnalytics\Metrics\ConversionRateMetric::class,
        'top_selling_products' => Mrorko840\AiAnalytics\Metrics\TopSellingProductsMetric::class,
    ],

    'security' => [
        'mask_fields' => ['email', 'phone', 'balance', 'card', 'national_id'],
        'allow_raw_sql' => false,
    ],

    'reports' => [
        'retention_days' => 30,
        'disk' => 'local',
        'path' => 'ai-analytics/reports',
    ],
];
