<?php

namespace Mrorko840\AiAnalytics\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserAnalyticsService
{
    public function getSignupsCount(Carbon $from, Carbon $to): int
    {
        $config = config('ai-analytics.entities.user');
        $modelClass = $config['model'] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return 0;
        }

        $createdAtColumn = $config['created_at_column'] ?? 'created_at';

        return $modelClass::whereBetween($createdAtColumn, [$from, $to])->count();
    }
}
