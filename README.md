# Laravel AI Analytics

A **UNIVERSAL AI-powered analytics and reporting package** for Laravel that can be installed into ANY Laravel project and configured to understand that project's business entities, metrics, events, and reports.

## Overview

This package is a production-grade analytics intelligence layer for Laravel. It provides:
1. Dynamic UI-driven database schema discovery and semantic table mapping.
2. Abstract entity resolution (Users, Orders, Products, Transactions) independent of your application codebase.
3. Pluggable Metric Registry system pulling from mapped data.
4. Natural language chat interface for analytics questions (without exposing raw SQL to AI).
5. Reporting engine with PDF, CSV, JSON and HTML exports.
6. Drop-in Blade UI for Dashboards, Data Sources configuration, Reports, and System Diagnostics.
7. Robust event tracking framework.

## Installation

1. Install the package via composer:
```bash
composer require mrorko840/laravel-ai-analytics
```

2. Run the interactive install command:
```bash
php artisan ai-analytics:install
```

## Security & Architecture

**Zero Raw SQL AI Execution**: The AI *never* generates SQL. 
AI Analytics uses a completely safe, deterministic process:
1. User provides a question.
2. AI extracts intent and matches it to your universally configured Metric Registry.
3. Safe PHP query abstractions parse metrics based on your UI-configured `ai_analytics_entity_mappings`.
4. Extracted data points are sent to the AI strictly to formulate English insights.
5. Sensitive fields are never queried automatically.

## Mapping Your Database

You **do not** need to re-write your application to fit this package. Instead, the package adapts to your schema.

1. Navigate to `/ai-analytics/data-sources` locally inside your Laravel App.
2. Inspect the schemas directly from the `Data Sources` menu.
3. Click a table (e.g., `customers` or `sales`).
4. Assign it a Semantic Entity role (e.g. `User` or `Order`).
5. Map necessary columns (`Primary Key`, `Created At`, `Amount`, etc) using the provided dropdowns.
6. Save your mapping.

*Mappings are written to `ai_analytics_entity_mappings` and will immediately power the Dashboard, Reports, and Chat Analytics.*

## System Diagnostics

Visit `/ai-analytics/diagnostics` at any time to check:
- Package health status.
- Remaining unmapped entities needed to power complex multi-table functions.
- Metric readiness flags.

## Event Tracking API

You can track arbitrary business events natively from the host app using the unified framework:

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
        // Query mapped models or custom query here
        return ['value' => 5.2];
    }
}
```

2. Register it in `config/ai-analytics.php` inside the `metrics` array OR strictly via `MetricRegistry->register()` in your AppServiceProvider.

## Testing

Run tests easily:
```bash
./vendor/bin/phpunit
```

## License

The MIT License (MIT).
