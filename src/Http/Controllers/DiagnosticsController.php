<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Mrorko840\AiAnalytics\Services\EntityMappingResolver;

class DiagnosticsController extends Controller
{
    private EntityMappingResolver $resolver;

    public function __construct(EntityMappingResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function index()
    {
        $coreEntities = ['user', 'order', 'product', 'order_item', 'transaction'];
        $diagnostics = [];

        foreach ($coreEntities as $entity) {
            try {
                $resolved = $this->resolver->resolveEntity($entity);
                $diagnostics[$entity] = [
                    'status' => $resolved ? 'ok' : 'missing',
                    'message' => $resolved ? "Mapped to {$resolved['source_type']}: " . ($resolved['table'] ?? $resolved['model']) : 'Not mapped',
                    'data' => $resolved
                ];
            } catch (\Exception $e) {
                $diagnostics[$entity] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        $health = [
            'metrics_ready' => !in_array('missing', array_column($diagnostics, 'status')),
            'app_debug' => config('app.debug'),
            'ai_provider' => config('ai-analytics.ai.provider'),
            'connection' => config('ai-analytics.database_connection', 'default'),
            'theme' => config('ai-analytics.ui.theme'),
        ];

        return view('ai-analytics::settings.diagnostics', compact('diagnostics', 'health'));
    }
}
