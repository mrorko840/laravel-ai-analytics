<?php

namespace Mrorko840\AiAnalytics\Console;

use Illuminate\Console\Command;
use Mrorko840\AiAnalytics\Services\MetricRegistry;
use Mrorko840\AiAnalytics\Models\AiAnalyticsMetricCache;

class CacheMetricsCommand extends Command
{
    protected $signature = 'ai-analytics:metrics:cache {--metric=}';
    protected $description = 'Warm up the metric caches';

    public function handle(MetricRegistry $registry): void
    {
        $this->info('Starting metric cache warmup...');

        $metricName = $this->option('metric');
        $metrics = $metricName ? [$registry->get($metricName)] : $registry->all();

        foreach ($metrics as $metric) {
            $this->info("Warming cache for {$metric->name()}...");

            $data = $metric->execute(); // Default filters

            AiAnalyticsMetricCache::create([
                'metric_name' => $metric->name(),
                'cache_key' => 'warmup_' . $metric->name(),
                'filters_hash' => md5(json_encode([])),
                'payload' => $data,
                'expires_at' => now()->addHours(1),
            ]);
        }

        $this->info('Complete!');
    }
}
