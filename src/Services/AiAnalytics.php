<?php

namespace Mrorko840\AiAnalytics\Services;

use Mrorko840\AiAnalytics\Models\AiAnalyticsEvent;

class AiAnalytics
{
    /**
     * Track a specific event in the analytics system.
     * 
     * @param string $eventType
     * @param array $payload
     * @return void
     */
    public function track(string $eventType, array $payload = []): void
    {
        if (!config('ai-analytics.tracking.enabled', true)) {
            return;
        }

        $event = new AiAnalyticsEvent();
        $event->event_type = $eventType;
        $event->event_key = $payload['event_key'] ?? null;
        $event->user_id = $payload['user_id'] ?? auth()->id() ?? null;
        $event->session_id = $payload['session_id'] ?? session()->getId() ?? null;
        $event->entity_type = $payload['entity_type'] ?? null;
        $event->entity_id = $payload['entity_id'] ?? null;

        // Remove standard fields from metadata and add the rest
        $metaKeys = ['event_key', 'user_id', 'session_id', 'entity_type', 'entity_id'];
        $metadata = array_diff_key($payload, array_flip($metaKeys));

        $event->metadata = count($metadata) > 0 ? $metadata : null;
        $event->occurred_at = now();
        $event->save();
    }
}
