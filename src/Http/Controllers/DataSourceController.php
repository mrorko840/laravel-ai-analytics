<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Mrorko840\AiAnalytics\Services\SchemaDiscoveryService;
use Mrorko840\AiAnalytics\Models\AiAnalyticsEntityMapping;

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
        $mappings = AiAnalyticsEntityMapping::where('is_active', true)->get();

        return view('ai-analytics::settings.data-sources', compact('tables', 'mappings'));
    }

    public function tableDetails(string $table)
    {
        $columns = $this->schemaService->getTableColumns($table);
        $entities = ['user', 'order', 'order_item', 'product', 'transaction', 'visit', 'product_view'];

        return view('ai-analytics::settings.table-details', compact('table', 'columns', 'entities'));
    }

    public function saveMapping(Request $request)
    {
        $request->validate([
            'entity_name' => 'required|string',
            'table_name' => 'required|string',
            'mapping' => 'required|array',
        ]);

        $entity = $request->input('entity_name');

        $mapping = AiAnalyticsEntityMapping::updateOrCreate(
            ['entity_name' => $entity],
            [
                'source_type' => 'table',
                'table_name' => $request->input('table_name'),
                'mapping' => $request->input('mapping'),
                'is_active' => true,
            ]
        );

        return redirect()->route('ai-analytics.data-sources.tables', $request->input('table_name'))
            ->with('success', 'Mapping saved successfully.');
    }
}
