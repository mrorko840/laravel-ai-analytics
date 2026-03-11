<?php

namespace Mrorko840\AiAnalytics\Chat;

use Mrorko840\AiAnalytics\Models\AiAnalyticsChat;
use Mrorko840\AiAnalytics\Models\AiAnalyticsMessage;

class ChatService
{
    protected IntentParser $intentParser;
    protected MetricResolver $metricResolver;
    protected InsightFormatter $insightFormatter;

    public function __construct(
        IntentParser $intentParser,
        MetricResolver $metricResolver,
        InsightFormatter $insightFormatter
    ) {
        $this->intentParser = $intentParser;
        $this->metricResolver = $metricResolver;
        $this->insightFormatter = $insightFormatter;
    }

    public function handleUserMessage(int $chatId, string $message): array
    {
        $chat = AiAnalyticsChat::findOrFail($chatId);

        // Save user message
        $chat->messages()->create([
            'role' => 'user',
            'content' => $message,
        ]);

        // Pipeline execution
        $intent = $this->intentParser->parse($message);

        $metricData = [];
        $replyContent = "I don't have enough data to answer that.";

        if (!empty($intent['metric_names'])) {
            $metricData = $this->metricResolver->resolveAndExecute($intent);
            $replyContent = $this->insightFormatter->format($message, $metricData);
        } else {
            // Not a data question, or generic
            $replyContent = "I'm not sure which metrics to look at for that question. Could you be more specific about the data you want (e.g. signups, revenue, deposits)?";
        }

        // Save assistant message
        $aiMessage = $chat->messages()->create([
            'role' => 'assistant',
            'content' => $replyContent,
            'meta' => [
                'intent' => $intent,
                'data' => $metricData,
            ]
        ]);

        return [
            'reply' => $aiMessage->content,
            'data' => $metricData,
            'intent' => $intent
        ];
    }

    public function createChat(?int $userId = null, ?string $title = null): AiAnalyticsChat
    {
        return AiAnalyticsChat::create([
            'user_id' => $userId,
            'title' => $title ?? 'New Conversation',
        ]);
    }
}
