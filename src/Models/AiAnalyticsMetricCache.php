<?php

namespace Mrorko840\AiAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class AiAnalyticsMetricCache extends Model
{
    protected $table = 'ai_analytics_metric_cache';

    protected $fillable = [
        'metric_name',
        'cache_key',
        'filters_hash',
        'payload',
        'expires_at',
    ];

    protected $casts = [
        'payload' => 'json',
        'expires_at' => 'datetime',
    ];
}
