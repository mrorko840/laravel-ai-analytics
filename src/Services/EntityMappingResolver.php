<?php

namespace Mrorko840\AiAnalytics\Services;

use Mrorko840\AiAnalytics\Models\AiAnalyticsEntityMapping;

class EntityMappingResolver
{
    /**
     * Resolve the mapping for a business entity, prioritizing database over config.
     *
     * @param string $entityName
     * @return array|null
     */
    public function resolveEntity(string $entityName): ?array
    {
        // Try getting from DB first (if table exists)
        try {
            $dbMapping = AiAnalyticsEntityMapping::where('entity_name', $entityName)
                ->where('is_active', true)
                ->first();

            if ($dbMapping) {
                return [
                    'source_type' => $dbMapping->source_type,
                    'model' => $dbMapping->model_class,
                    'table' => $dbMapping->table_name,
                    'mapping' => $dbMapping->mapping ?? [],
                ];
            }
        } catch (\Exception $e) {
            // Migrations might not be run or DB unavailable, fallback to config silently
        }

        // Fallback to Config
        $configMapping = config("ai-analytics.entities.{$entityName}");
        
        if ($configMapping) {
            return [
                'source_type' => isset($configMapping['model']) ? 'model' : 'table',
                'model' => $configMapping['model'] ?? null,
                'table' => $configMapping['table'] ?? null,
                'mapping' => $configMapping,
            ];
        }

        return null; // No mapping found
    }

    public function getQueryBuilder(string $entityName)
    {
        $resolved = $this->resolveEntity($entityName);

        if (!$resolved) {
            throw new \Exception("Entity mapping for [{$entityName}] is missing.");
        }

        if ($resolved['source_type'] === 'model' && !empty($resolved['model']) && class_exists($resolved['model'])) {
            return $resolved['model']::query();
        } elseif (!empty($resolved['table'])) {
            return \Illuminate\Support\Facades\DB::table($resolved['table']);
        }

        throw new \Exception("Entity mapping for [{$entityName}] lacks a valid model or table configuration.");
    }
}
