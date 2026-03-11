<?php

namespace Mrorko840\AiAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class AiAnalyticsDataSource extends Model
{
    protected $table = 'ai_analytics_data_sources';

    protected $fillable = [
        'connection_name',
        'table_name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];
}
