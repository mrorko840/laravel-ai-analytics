<?php

namespace Mrorko840\AiAnalytics\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserAnalyticsService
{
    private EntityMappingResolver $resolver;

    public function __construct(EntityMappingResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getSignupsCount(Carbon $from, Carbon $to): int
    {
        $resolved = $this->resolver->resolveEntity('user');
        
        if (!$resolved) {
            throw new \Exception("User entity mapping is missing.");
        }

        $createdAtColumn = $resolved['mapping']['created_at_column'] ?? 'created_at';
        $query = $this->resolver->getQueryBuilder('user');
        
        return $query->whereBetween($createdAtColumn, [$from, $to])->count();
    }
}
