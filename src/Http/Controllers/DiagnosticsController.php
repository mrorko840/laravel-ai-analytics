<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Mrorko840\AiAnalytics\Models\AiAnalyticsDataSource;
use Mrorko840\AiAnalytics\Models\AiAnalyticsCard;
use Mrorko840\AiAnalytics\Services\SchemaDiscoveryService;

class DiagnosticsController extends Controller
{
    private SchemaDiscoveryService $schemaService;

    public function __construct(SchemaDiscoveryService $schemaService)
    {
        $this->schemaService = $schemaService;
    }

    public function index()
    {
        $allTables = $this->schemaService->getAllTables();
        $enabledTables = AiAnalyticsDataSource::where('is_enabled', true)->get();
        $cards = AiAnalyticsCard::count();

        $health = [
            'metrics_ready' => $enabledTables->count() > 0,
            'app_debug' => config('app.debug'),
            'ai_provider' => config('ai-analytics.ai.provider'),
            'connection' => config('ai-analytics.database_connection', 'default'),
            'theme' => config('ai-analytics.ui.theme'),
        ];

        return view('ai-analytics::settings.diagnostics', compact('health', 'allTables', 'enabledTables', 'cards'));
    }
}
