<?php

namespace Mrorko840\AiAnalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiAnalyticsChat extends Model
{
    protected $table = 'ai_analytics_chats';

    protected $fillable = [
        'user_id',
        'title',
        'context',
    ];

    protected $casts = [
        'context' => 'json',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(AiAnalyticsMessage::class, 'chat_id');
    }
}
