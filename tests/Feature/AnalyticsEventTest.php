<?php

namespace Mrorko840\AiAnalytics\Tests\Feature;

use Mrorko840\AiAnalytics\Tests\TestCase;
use Mrorko840\AiAnalytics\Services\AiAnalytics;
use Mrorko840\AiAnalytics\Models\AiAnalyticsEvent;

class AnalyticsEventTest extends TestCase
{
    public function test_it_tracks_custom_events()
    {
        $service = app(AiAnalytics::class);

        $service->track('button_clicked', [
            'button_id' => 'checkout',
            'session_id' => '12345'
        ]);

        $this->assertDatabaseHas('ai_analytics_events', [
            'event_type' => 'button_clicked',
            'session_id' => '12345'
        ]);

        $event = AiAnalyticsEvent::first();
        $this->assertEquals('checkout', $event->metadata['button_id']);
    }

    public function test_helper_method_works()
    {
        aiAnalytics()->track('page_view', ['url' => '/dashboard']);

        $this->assertDatabaseHas('ai_analytics_events', [
            'event_type' => 'page_view',
        ]);

        $event = AiAnalyticsEvent::where('event_type', 'page_view')->first();
        $this->assertEquals('/dashboard', $event->metadata['url']);
    }
}
