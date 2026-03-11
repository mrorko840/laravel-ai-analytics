<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Mrorko840\AiAnalytics\Models\AiAnalyticsChat;
use Mrorko840\AiAnalytics\Models\AiAnalyticsMessage;
use Mrorko840\AiAnalytics\Services\AIQueryService;

class ChatController extends Controller
{
    private AIQueryService $aiQueryService;

    public function __construct(AIQueryService $aiQueryService)
    {
        $this->aiQueryService = $aiQueryService;
    }

    public function index()
    {
        $sessions = AiAnalyticsChat::orderBy('updated_at', 'desc')->get();
        return view('ai-analytics::chat', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $chat = AiAnalyticsChat::create([
            'title' => substr($request->input('message'), 0, 50) . '...',
        ]);

        return redirect()->route('ai-analytics.chat', ['session' => $chat->id]);
    }

    public function message(Request $request, string $chatId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $chat = AiAnalyticsChat::findOrFail($chatId);
        $userMessage = $request->input('message');

        // Save User Message
        AiAnalyticsMessage::create([
            'ai_analytics_chat_id' => $chat->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        // Process SQL & AI Generation Flow
        $responsePayload = $this->aiQueryService->executePrompt($userMessage);

        $replyContent = $responsePayload['reply'];

        // Optionally, append the generated SQL text securely in debug mode or hidden
        $metaData = [
            'sql' => $responsePayload['sql'],
            'data_points' => count($responsePayload['data'] ?? []),
        ];

        // Save AI Message
        AiAnalyticsMessage::create([
            'ai_analytics_chat_id' => $chat->id,
            'role' => 'assistant',
            'content' => $replyContent,
        ]);

        // Touch the chat to update updated_at
        $chat->touch();

        return response()->json([
            'status' => 'success',
            'reply' => $replyContent,
            'sql' => config('app.debug') ? $responsePayload['sql'] : null, // Show SQL in UI if debug
        ]);
    }
}
