<?php

namespace Mrorko840\AiAnalytics\Services;

class QueryGuardService
{
    /**
     * Ensure the query is a SELECT statement and doesn't contain destructive commands.
     *
     * @param string $sql
     * @return bool
     */
    public function isSafe(string $sql): bool
    {
        $sql = trim($sql);
        
        // Basic check: must start with SELECT
        if (!preg_match('/^SELECT\b/i', $sql)) {
            return false;
        }

        // List of forbidden words
        $forbidden = [
            'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE',
            'REPLACE', 'GRANT', 'REVOKE', 'CALL', 'EXECUTE', 'EXEC'
        ];

        foreach ($forbidden as $word) {
            if (preg_match("/\b{$word}\b/i", $sql)) {
                return false;
            }
        }

        return true;
    }
}
