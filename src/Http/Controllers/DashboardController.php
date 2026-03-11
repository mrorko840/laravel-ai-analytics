<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Mrorko840\AiAnalytics\Models\AiAnalyticsCard;
use Mrorko840\AiAnalytics\Models\AiAnalyticsDataSource;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        $cards = AiAnalyticsCard::orderBy('order_column')->get();
        $enabledSources = AiAnalyticsDataSource::where('is_enabled', true)->count();
        $connectionName = config('ai-analytics.database_connection');
        
        $cardData = [];
        
        foreach ($cards as $card) {
            try {
                $query = DB::connection($connectionName)->table($card->table_name);
                
                $value = 0;
                switch ($card->aggregation_type) {
                    case 'COUNT':
                        $value = $card->column_name ? $query->count($card->column_name) : $query->count();
                        break;
                    case 'SUM':
                        $value = $query->sum($card->column_name);
                        break;
                    case 'AVG':
                        $value = $query->avg($card->column_name);
                        break;
                    case 'MAX':
                        $value = $query->max($card->column_name);
                        break;
                    case 'MIN':
                        $value = $query->min($card->column_name);
                        break;
                }
                
                $cardData[] = [
                    'card' => $card,
                    'value' => is_numeric($value) ? round((float)$value, 2) : $value,
                    'is_currency' => $card->aggregation_type === 'SUM' && str_contains(strtolower($card->column_name ?? ''), 'amount'),
                    'error' => null
                ];
                
            } catch (Exception $e) {
                report($e);
                $cardData[] = [
                    'card' => $card,
                    'value' => '--',
                    'is_currency' => false,
                    'error' => 'Config/SQL error'
                ];
            }
        }

        return view('ai-analytics::dashboard', compact('cardData', 'enabledSources'));
    }
}
