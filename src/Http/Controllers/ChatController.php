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

        $userMessage = $request->input('message');

        $chat = AiAnalyticsChat::create([
            'title' => substr($userMessage, 0, 50) . '...',
        ]);

        return $this->processMessage($request, $chat, $userMessage);
    }

    public function message(Request $request, string $chatId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $chat = AiAnalyticsChat::findOrFail($chatId);
        $userMessage = $request->input('message');

        return $this->processMessage($request, $chat, $userMessage);
    }

    private function processMessage(Request $request, AiAnalyticsChat $chat, string $userMessage)
    {
        try {
            // Save User Message locally utilizing the correct relationship ID key
            AiAnalyticsMessage::create([
                'chat_id' => $chat->id,
                'role' => 'user',
                'content' => $userMessage,
            ]);

            // Process SQL & AI Generation Flow
            $responsePayload = $this->aiQueryService->executePrompt($userMessage);

            $replyContent = $responsePayload['reply'] ?? "I couldn't process this request.";
            $sql = $responsePayload['sql'] ?? null;

            // Save AI Message securely
            AiAnalyticsMessage::create([
                'chat_id' => $chat->id,
                'role' => 'assistant',
                'content' => $replyContent,
            ]);

            // Touch the chat to update updated_at
            $chat->touch();

            return response()->json([
                'status' => 'success',
                'chat_id' => $chat->id, // Send ID back to frontend if they started a brand new chat
                'reply' => $replyContent,
                'sql' => config('app.debug') ? $sql : null, // Display SQL safely in debug interface
            ]);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred while processing your request inside the Chat Controller engine.',
            ], 500);
        }
    }
}
