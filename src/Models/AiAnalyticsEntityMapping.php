<?php

namespace Mrorko840\AiAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class AiAnalyticsEntityMapping extends Model
{
    protected $table = 'ai_analytics_entity_mappings';

    protected $fillable = [
        'entity_name',
        'source_type',
        'model_class',
        'table_name',
        'mapping',
        'is_active',
    ];

    protected $casts = [
        'mapping' => 'json',
        'is_active' => 'boolean',
    ];
}
