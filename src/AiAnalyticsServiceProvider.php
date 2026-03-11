<?php

namespace Mrorko840\AiAnalytics;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Mrorko840\AiAnalytics\Console\InstallCommand;
use Mrorko840\AiAnalytics\Console\CacheMetricsCommand;
use Mrorko840\AiAnalytics\Console\GenerateReportCommand;
use Mrorko840\AiAnalytics\Services\AiAnalytics;
use Mrorko840\AiAnalytics\Services\MetricRegistry;
use Mrorko840\AiAnalytics\Contracts\AiProviderInterface;
use Mrorko840\AiAnalytics\Chat\OpenAiProvider;

class AiAnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai-analytics.php', 'ai-analytics');

        $this->app->singleton(AiAnalytics::class, function ($app) {
            return new AiAnalytics();
        });

        $this->app->singleton(MetricRegistry::class, function ($app) {
            return new MetricRegistry();
        });

        $this->app->bind(AiProviderInterface::class, function ($app) {
            return new OpenAiProvider(config('ai-analytics.ai.api_key'));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishResources();
            $this->registerCommands();
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ai-analytics');
        $this->registerRoutes();
        $this->bootMetrics();
    }

    protected function publishResources(): void
    {
        $this->publishes([
            __DIR__ . '/../config/ai-analytics.php' => config_path('ai-analytics.php'),
        ], 'ai-analytics-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'ai-analytics-migrations');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/ai-analytics'),
        ], 'ai-analytics-views');
    }

    protected function registerCommands(): void
    {
        $this->commands([
            InstallCommand::class,
            CacheMetricsCommand::class,
            GenerateReportCommand::class,
        ]);
    }

    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });

        Route::group($this->apiRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('ai-analytics.route_prefix', 'ai-analytics'),
            'middleware' => config('ai-analytics.middleware', ['web', 'auth']),
        ];
    }
    
    protected function apiRouteConfiguration(): array
    {
        return [
            'prefix' => config('ai-analytics.route_prefix', 'ai-analytics') . '/api',
            'middleware' => config('ai-analytics.api_middleware', ['api', 'auth:sanctum']),
        ];
    }

    protected function bootMetrics(): void
    {
        $registry = $this->app->make(MetricRegistry::class);
        $metrics = config('ai-analytics.metrics', []);
        foreach ($metrics as $name => $class) {
            $registry->register($name, $this->app->make($class));
        }
    }
}
