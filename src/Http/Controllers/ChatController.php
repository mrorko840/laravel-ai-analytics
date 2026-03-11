<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Mrorko840\AiAnalytics\Chat\ChatService;
use Mrorko840\AiAnalytics\Models\AiAnalyticsChat;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        $chats = AiAnalyticsChat::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('ai-analytics::chat', compact('chats'));
    }

    public function store(Request $request)
    {
        $chat = $this->chatService->createChat(auth()->id(), 'New Conversation');
        return redirect()->route('ai-analytics.chat', ['chat' => $chat->id]);
    }

    public function message(Request $request, $chatId)
    {
        $request->validate(['message' => 'required|string']);

        $response = $this->chatService->handleUserMessage($chatId, $request->input('message'));

        if ($request->wantsJson()) {
            return response()->json($response);
        }

        return back()->with('response', $response);
    }

    public function apiMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        $response = $this->chatService->handleUserMessage($request->input('chat_id'), $request->input('message'));
        return response()->json($response);
    }
}
