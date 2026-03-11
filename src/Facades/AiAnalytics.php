<?php

namespace Mrorko840\AiAnalytics\Facades;

use Illuminate\Support\Facades\Facade;

class AiAnalytics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mrorko840\AiAnalytics\Services\AiAnalytics::class;
    }
}
