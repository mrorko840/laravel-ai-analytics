# Laravel AI Analytics

A **UNIVERSAL AI-powered analytics and reporting package** for Laravel that can be installed into ANY Laravel project and configured to understand that project's business entities, metrics, events, and reports.

## Overview

This package is a production-grade analytics intelligence layer for Laravel. It provides:
1. Config-driven abstract entity mapping (Users, Orders, Products).
2. Pluggable Metric Registry system.
3. Natural language chat interface for analytics questions (without exposing raw SQL to AI).
4. Reporting engine with PDF, CSV, JSON and HTML exports.
5. Simple drop-in Blade UI for Dashboards, Reports, and Chat.
6. Robust event tracking framework.

## Installation

1. Install the package via composer:
```bash
composer require mrorko840/laravel-ai-analytics
```

2. Run the interactive install command:
```bash
php artisan ai-analytics:install
```
This will publish the configuration (`config/ai-analytics.php`) and standard database migrations.

3. Complete installation by running migrations (if you didn't do it via the command):
```bash
php artisan migrate
```

## Configuration

In your `.env` file, configure your AI Provider:

```env
AI_ANALYTICS_ENABLED=true
AI_ANALYTICS_PROVIDER=openai
AI_ANALYTICS_MODEL=gpt-4o
AI_ANALYTICS_API_KEY=sk-your-openai-api-key
```

### Entity Mapping (Very Important)

Open `config/ai-analytics.php`. This package relies on dynamic entity mapping so it works with ANY schema. You must map your host application's models to the standard analytics entities.

```php
'entities' => [
    'user' => [
        'model' => App\Models\User::class,
        'created_at_column' => 'created_at',
    ],
    'order' => [
        'model' => App\Models\Order::class,
        'amount_column' => 'grand_total', // Your specific amount column
        'status_column' => 'order_status',
        'paid_statuses' => ['paid', 'shipped', 'delivered'],
    ],
    // Map product, transaction, visit, product_view...
]
```

## Security & Architecture

**Zero Raw SQL AI Execution**: The AI *never* generates SQL. It parses intent from the unified Metric Registry, determines the context, and instructs secure service-layer metrics to execute and return strict data arrays.

## Event Tracking API

You can track business events natively from the host app:

```php
// With Facade
use Mrorko840\AiAnalytics\Facades\AiAnalytics;
AiAnalytics::track('signup_completed', ['source' => 'web']);

// With Helper
aiAnalytics()->track('product_viewed', [
    'product_id' => 15,
    'user_id' => auth()->id(),
    'page' => request()->fullUrl(),
]);
```

## Adding Custom Metrics

1. Create a class extending `AbstractMetric`:
```php
class CustomChurnMetric extends \Mrorko840\AiAnalytics\Metrics\AbstractMetric {
    public function name(): string { return 'churn_rate'; }
    public function label(): string { return 'Monthly Churn Rate'; }
    public function description(): string { return 'Calculates percentage of users who cancelled.'; }
    public function supportedFilters(): array { return ['month']; }
    public function execute(array $filters = []): mixed {
        return ['value' => 5.2]; // Your business logic here
    }
}
```

2. Register it in `config/ai-analytics.php` inside the `metrics` array OR strictly via `MetricRegistry->register()` in your AppServiceProvider.

## Accessing the UI

Visit your configured route prefix, by default: `/ai-analytics/dashboard`

## Testing

Run tests easily:
```bash
./vendor/bin/phpunit
```

## License

The MIT License (MIT).
