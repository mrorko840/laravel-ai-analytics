<?php

namespace Mrorko840\AiAnalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAnalyticsMessage extends Model
{
    protected $table = 'ai_analytics_messages';

    protected $fillable = [
        'chat_id',
        'role',
        'content',
        'meta',
    ];

    protected $casts = [
        'meta' => 'json',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(AiAnalyticsChat::class, 'chat_id');
    }
}
