<?php

namespace Mrorko840\AiAnalytics\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Mrorko840\AiAnalytics\AiAnalyticsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            AiAnalyticsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Setup default config for entities to be safe
        $app['config']->set('ai-analytics.tracking.enabled', true);
    }

    protected function setUpDatabase($app)
    {
        $migration = include __DIR__ . '/../database/migrations/2024_01_01_000001_create_ai_analytics_tables.php';
        $migration->up();
    }
}
