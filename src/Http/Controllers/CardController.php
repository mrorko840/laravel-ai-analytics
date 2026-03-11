<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Mrorko840\AiAnalytics\Models\AiAnalyticsCard;
use Mrorko840\AiAnalytics\Services\SchemaDiscoveryService;
use Mrorko840\AiAnalytics\Models\AiAnalyticsDataSource;

class CardController extends Controller
{
    protected SchemaDiscoveryService $schemaService;

    public function __construct(SchemaDiscoveryService $schemaService)
    {
        $this->schemaService = $schemaService;
    }

    public function index()
    {
        $cards = AiAnalyticsCard::orderBy('order_column')->get();
        return view('ai-analytics::cards.index', compact('cards'));
    }

    public function create()
    {
        $enabledTables = AiAnalyticsDataSource::where('is_enabled', true)->pluck('table_name')->toArray();
        $tableColumns = [];
        
        foreach ($enabledTables as $table) {
            $tableColumns[$table] = $this->schemaService->getTableColumns($table);
        }

        return view('ai-analytics::cards.create', compact('enabledTables', 'tableColumns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'table_name' => 'required|string',
            'column_name' => 'nullable|string',
            'aggregation_type' => 'required|string|in:COUNT,SUM,AVG,MAX,MIN',
            'filters' => 'nullable|array',
            'filters.*.column' => 'required_with:filters|string',
            'filters.*.operator' => 'required_with:filters|string',
            'filters.*.value' => 'nullable|string',
        ]);

        $maxOrder = AiAnalyticsCard::max('order_column') ?? 0;

        AiAnalyticsCard::create([
            'name' => $request->name,
            'description' => $request->description,
            'table_name' => $request->table_name,
            'column_name' => $request->column_name,
            'aggregation_type' => $request->aggregation_type,
            'filters' => $request->filters,
            'order_column' => $maxOrder + 1,
        ]);

        return redirect()->route('ai-analytics.dashboard')->with('success', 'Card created successfully.');
    }

    public function edit(AiAnalyticsCard $card)
    {
        $enabledTables = AiAnalyticsDataSource::where('is_enabled', true)->pluck('table_name')->toArray();
        $tableColumns = [];
        
        foreach ($enabledTables as $table) {
            $tableColumns[$table] = $this->schemaService->getTableColumns($table);
        }

        return view('ai-analytics::cards.edit', compact('card', 'enabledTables', 'tableColumns'));
    }

    public function update(Request $request, AiAnalyticsCard $card)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'table_name' => 'required|string',
            'column_name' => 'nullable|string',
            'aggregation_type' => 'required|string|in:COUNT,SUM,AVG,MAX,MIN',
            'filters' => 'nullable|array',
            'filters.*.column' => 'required_with:filters|string',
            'filters.*.operator' => 'required_with:filters|string',
            'filters.*.value' => 'nullable|string',
        ]);

        $data = $request->all();
        // If filters are completely unset/removed, force null correctly instead of dropping the trait
        $data['filters'] = $request->input('filters', null);

        $card->update($data);

        return redirect()->route('ai-analytics.dashboard')->with('success', 'Card updated successfully.');
    }

    public function destroy(AiAnalyticsCard $card)
    {
        $card->delete();
        return redirect()->route('ai-analytics.dashboard')->with('success', 'Card deleted successfully.');
    }
}
