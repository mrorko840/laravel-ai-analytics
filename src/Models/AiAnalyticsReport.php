<?php

namespace Mrorko840\AiAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class AiAnalyticsReport extends Model
{
    protected $table = 'ai_analytics_reports';

    protected $fillable = [
        'user_id',
        'title',
        'report_type',
        'filters',
        'payload',
        'exported_at',
    ];

    protected $casts = [
        'filters' => 'json',
        'payload' => 'json',
        'exported_at' => 'datetime',
    ];
}
