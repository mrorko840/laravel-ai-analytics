<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Mrorko840\AiAnalytics\Services\MetricRegistry;
use Exception;

class DashboardController extends Controller
{
    public function index(MetricRegistry $registry)
    {
        $metrics = [];
        $errors = [];
        
        $metricKeys = [
            'signups_count',
            'revenue',
            'deposits',
            'withdrawals',
            'conversion_rate',
            'top_selling_products'
        ];

        foreach ($metricKeys as $key) {
            try {
                $metrics[$key] = $registry->get($key)->execute();
            } catch (Exception $e) {
                report($e);
                $metrics[$key] = null;
                $errors[$key] = $e->getMessage();
            }
        }

        return view('ai-analytics::dashboard', compact('metrics', 'errors'));
    }
}
