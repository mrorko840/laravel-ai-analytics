<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Mrorko840\AiAnalytics\Services\SchemaDiscoveryService;
use Mrorko840\AiAnalytics\Models\AiAnalyticsDataSource;

class DataSourceController extends Controller
{
    private SchemaDiscoveryService $schemaService;

    public function __construct(SchemaDiscoveryService $schemaService)
    {
        $this->schemaService = $schemaService;
    }

    public function index()
    {
        $tables = $this->schemaService->getAllTables();
        $enabledSources = AiAnalyticsDataSource::all()->keyBy('table_name');

        $tableDetails = [];
        foreach ($tables as $table) {
            $tableDetails[$table] = [
                'columns' => $this->schemaService->getTableColumns($table),
                'is_enabled' => isset($enabledSources[$table]) ? $enabledSources[$table]->is_enabled : false,
            ];
        }

        return view('ai-analytics::settings.data-sources', compact('tableDetails'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string',
            'is_enabled' => 'required|boolean',
        ]);

        AiAnalyticsDataSource::updateOrCreate(
            ['table_name' => $request->input('table_name')],
            ['is_enabled' => $request->input('is_enabled')]
        );

        return response()->json(['success' => true]);
    }
}
