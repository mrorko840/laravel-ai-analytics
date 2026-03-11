<?php

namespace Mrorko840\AiAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

class AiAnalyticsCard extends Model
{
    protected $table = 'ai_analytics_cards';

    protected $fillable = [
        'name',
        'description',
        'table_name',
        'column_name',
        'aggregation_type',
        'order_column',
    ];
}
