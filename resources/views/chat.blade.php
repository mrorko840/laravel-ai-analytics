@extends('ai-analytics::layout')

@section('content')
    <div class="flex h-[80vh] bg-white rounded-xl shadow-lg border overflow-hidden">
        <!-- Sidebar -->
        <div class="w-1/4 bg-gray-50 border-r flex flex-col">
            <div class="p-4 border-b">
                <form action="{{ route('ai-analytics.chat.store') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full bg-white border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg shadow-sm hover:bg-gray-50 transition">
                        + New Chat
                    </button>
                </form>
            </div>
            <div class="flex-grow overflow-y-auto">
                @foreach($chats as $c)
                    <a href="?chat={{ $c->id }}"
                        class="block p-4 border-b hover:bg-gray-100 {{ request('chat') == $c->id ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $c->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $c->updated_at->diffForHumans() }}</p>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="w-3/4 flex flex-col">
            @php
                $activeChat = null;
                if (request('chat')) {
                    $activeChat = $chats->firstWhere('id', request('chat'));
                }
            @endphp

            <!-- Messages Area -->
            <div class="flex-grow p-6 overflow-y-auto bg-gray-50" id="chat-messages">
                @if($activeChat)
                    @foreach($activeChat->messages as $msg)
                        <div class="mb-6 flex {{ $msg->role === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="'max-w-xl rounded-2xl p-4 ' . {{ $msg->role === 'user' ? 'class="bg-blue-600 text-white"' : 'class="bg-white shadow-sm border text-gray-800"' }}">
                                <div class="prose {{ $msg->role === 'user' ? 'prose-invert' : '' }} text-sm max-w-none">
                                    {!! nl2br(e($msg->content)) !!}
                                </div>

                                @if($msg->role === 'assistant' && !empty($msg->meta['data']))
                                    <div class="mt-4 p-3 bg-gray-50 rounded border text-xs overflow-x-auto text-gray-700">
                                        <pre>Based on {{ count($msg->meta['data']) }} data points analyzed.</pre>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="h-full flex items-center justify-center text-gray-400 flex-col">
                        <svg class="h-16 w-16 mb-4 text-blue-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <h2 class="text-xl font-medium text-gray-600">AI Analytics Assistant</h2>
                        <p class="mt-2 text-sm">Create a new chat to ask questions about your data.</p>
                    </div>
                @endif
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-white border-t">
                @if($activeChat)
                    <form action="{{ route('ai-analytics.chat.message', $activeChat->id) }}" method="POST"
                        class="flex items-end shadow-sm border rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 bg-gray-50 p-2">
                        @csrf
                        <textarea name="message" rows="2"
                            class="w-full bg-transparent border-0 focus:ring-0 resize-none flex-grow p-2"
                            placeholder="Ask about revenue, signups, products..."></textarea>
                        <button type="submit"
                            class="m-2 bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition flex-shrink-0 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Auto scroll to bottom of chat
        const messages = document.getElementById('chat-messages');
        if (messages) messages.scrollTop = messages.scrollHeight;
    </script>
@endsection