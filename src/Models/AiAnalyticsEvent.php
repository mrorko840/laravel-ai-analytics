<?php

namespace Mrorko840\AiAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class AiAnalyticsEvent extends Model
{
    protected $table = 'ai_analytics_events';

    protected $fillable = [
        'event_type',
        'event_key',
        'user_id',
        'session_id',
        'entity_type',
        'entity_id',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'json',
        'occurred_at' => 'datetime',
    ];
}
