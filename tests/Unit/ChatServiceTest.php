<?php

namespace Mrorko840\AiAnalytics\Tests\Unit;

use Mrorko840\AiAnalytics\Tests\TestCase;
use Mrorko840\AiAnalytics\Contracts\AiProviderInterface;
use Mrorko840\AiAnalytics\Chat\ChatService;
use Mrorko840\AiAnalytics\Chat\IntentParser;
use Mrorko840\AiAnalytics\Chat\MetricResolver;
use Mrorko840\AiAnalytics\Chat\InsightFormatter;
use Mrorko840\AiAnalytics\Services\MetricRegistry;
use Mockery;

class ChatServiceTest extends TestCase
{
    public function test_it_handles_user_message_and_returns_insight_with_data()
    {
        // Mock Provider
        $providerMock = Mockery::mock(AiProviderInterface::class);
        $providerMock->shouldReceive('parseIntent')->andReturn([
            'metric_names' => ['test_metric'],
            'filters' => []
        ]);
        $providerMock->shouldReceive('ask')->andReturn('Insight summary from AI.');

        // Simple Registry implementation with a fake metric
        $registry = new MetricRegistry();
        $fakeMetric = new class implements \Mrorko840\AiAnalytics\Contracts\MetricInterface {
            public function name(): string
            {
                return 'test_metric';
            }
            public function label(): string
            {
                return 'Test Metric';
            }
            public function description(): string
            {
                return 'A test metric.';
            }
            public function supportedFilters(): array
            {
                return [];
            }
            public function execute(array $filters = []): mixed
            {
                return ['value' => 100];
            }
        };
        $registry->register('test_metric', $fakeMetric);

        // Build Service
        $intentParser = new IntentParser($providerMock, $registry);
        $metricResolver = new MetricResolver($registry);
        $formatter = new InsightFormatter($providerMock);

        $chatService = new ChatService($intentParser, $metricResolver, $formatter);

        // Assertions
        $chat = $chatService->createChat(1, 'Test');
        $response = $chatService->handleUserMessage($chat->id, 'Show me test data');

        $this->assertEquals('Insight summary from AI.', $response['reply']);
        $this->assertArrayHasKey('test_metric', $response['data']);
        $this->assertEquals(100, $response['data']['test_metric']['data']['value']);
    }
}
