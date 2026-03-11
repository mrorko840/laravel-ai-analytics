<?php

namespace Mrorko840\AiAnalytics\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchemaDiscoveryService
{
    /**
     * Get all tables from the default or configured database connection.
     */
    public function getAllTables(): array
    {
        $connectionName = config('ai-analytics.database_connection');
        $connection = DB::connection($connectionName);
        $driver = $connection->getDriverName();
        $tables = [];

        try {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                $results = $connection->select('SHOW TABLES');
                foreach ($results as $row) {
                    $tables[] = current((array) $row);
                }
            } elseif ($driver === 'sqlite') {
                $results = $connection->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($results as $row) {
                    $tables[] = $row->name;
                }
            } elseif ($driver === 'pgsql') {
                $results = $connection->select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema'");
                foreach ($results as $row) {
                    $tables[] = $row->tablename;
                }
            } else {
                // Fallback for Laravel 11+
                if (method_exists(Schema::connection($connectionName), 'getTables')) {
                    $schemaTables = Schema::connection($connectionName)->getTables();
                    foreach ($schemaTables as $tableInfo) {
                        $tables[] = $tableInfo['name'];
                    }
                }
            }
        } catch (\Exception $e) {
            // Handle error silently, returning empty array
        }

        return array_values(array_filter($tables));
    }

    /**
     * Get all columns for a specific table.
     */
    public function getTableColumns(string $table): array
    {
        $connectionName = config('ai-analytics.database_connection');
        try {
            return Schema::connection($connectionName)->getColumnListing($table);
        } catch (\Exception $e) {
            return [];
        }
    }
}
