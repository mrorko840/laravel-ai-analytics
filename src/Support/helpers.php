<?php

use Mrorko840\AiAnalytics\Services\AiAnalytics;

if (!function_exists('aiAnalytics')) {
    /**
     * Get the AiAnalytics service instance.
     *
     * @return \Mrorko840\AiAnalytics\Services\AiAnalytics
     */
    function aiAnalytics(): AiAnalytics
    {
        return app(AiAnalytics::class);
    }
}
