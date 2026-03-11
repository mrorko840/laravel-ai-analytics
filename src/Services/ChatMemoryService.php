<?php

namespace Mrorko840\AiAnalytics\Services;

use Mrorko840\AiAnalytics\Models\AiAnalyticsChat;
use Mrorko840\AiAnalytics\Models\AiAnalyticsMessage;

class ChatMemoryService
{
    /**
     * Get the recent conversation history mapped properly for an AI context window.
     * Retains up to the last 20 messages to prevent token overflow.
     *
     * @param AiAnalyticsChat $chat
     * @param int $limit
     * @return array
     */
    public function getRecentHistory(AiAnalyticsChat $chat, int $limit = 20): array
    {
        $messages = $chat->messages()
            ->latest()
            ->take($limit)
            ->get()
            ->reverse(); // We want oldest to newest historically

        $history = [];
        foreach ($messages as $msg) {
            $history[] = [
                'role' => $msg->role === 'assistant' ? 'assistant' : 'user', // strictly defined mapped roles
                'content' => $msg->content,
            ];
        }

        return $history;
    }

    /**
     * Builds the final LLM prompt payload utilizing explicit context histories.
     * 
     * @param AiAnalyticsChat $chat
     * @param string $systemPrompt
     * @param string $currentMessage
     * @return array
     */
    public function buildPromptWithMemory(AiAnalyticsChat $chat, string $systemPrompt, string $currentMessage): array
    {
        $history = $this->getRecentHistory($chat);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Append past history memory
        foreach ($history as $historyItem) {
            $messages[] = $historyItem;
        }

        // Add the current prompt
        $messages[] = ['role' => 'user', 'content' => $currentMessage];

        return $messages;
    }
}
