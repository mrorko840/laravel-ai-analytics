<?php

namespace Mrorko840\AiAnalytics\Services;

use Carbon\Carbon;
use InvalidArgumentException;

class DateRangeResolverService
{
    /**
     * Resolves a predefined string preset into explicit Carbon from/to boundaries mapping safely.
     * Returns an array with safe UI labels and DB boundary parameters.
     *
     * @param string $preset
     * @return array
     */
    public function resolvePreset(string $preset): array
    {
        $now = now();

        switch (strtolower($preset)) {
            case 'today':
                return [
                    'label' => 'Today',
                    'from' => $now->copy()->startOfDay(),
                    'to' => $now->copy()->endOfDay(),
                ];
            case 'yesterday':
                return [
                    'label' => 'Yesterday',
                    'from' => $now->copy()->subDay()->startOfDay(),
                    'to' => $now->copy()->subDay()->endOfDay(),
                ];
            case 'last_7_days':
                return [
                    'label' => 'Last 7 Days',
                    'from' => $now->copy()->subDays(6)->startOfDay(),
                    'to' => $now->copy()->endOfDay(),
                ];
            case 'last_30_days':
                return [
                    'label' => 'Last 30 Days',
                    'from' => $now->copy()->subDays(29)->startOfDay(),
                    'to' => $now->copy()->endOfDay(),
                ];
            case 'this_month':
                return [
                    'label' => 'This Month',
                    'from' => $now->copy()->startOfMonth(),
                    'to' => $now->copy()->endOfMonth(),
                ];
            case 'last_month':
                return [
                    'label' => 'Last Month',
                    'from' => $now->copy()->subMonth()->startOfMonth(),
                    'to' => $now->copy()->subMonth()->endOfMonth(),
                ];
            case 'lifetime':
            case 'all_time':
                return [
                    'label' => 'All Time',
                    'from' => null,
                    'to' => $now->copy()->endOfDay(),
                ];
            default:
                // Fallback to strict standard parsing if somehow a literal valid Y-m-d H:i:s is strictly injected explicitly
                try {
                    $custom = Carbon::parse($preset);
                    return [
                        'label' => $custom->toDateString(),
                        'from' => $custom->copy()->startOfDay(),
                        'to' => $custom->copy()->endOfDay(),
                    ];
                } catch (\Exception $e) {
                    return [
                        'label' => 'Custom Range',
                        'from' => null,
                        'to' => $now->copy()->endOfDay(),
                    ];
                }
        }
    }
}
