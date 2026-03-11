@extends('ai-analytics::layout')

@section('content')
<div class="h-[calc(100vh-100px)] flex bg-white border rounded-xl overflow-hidden shadow-sm">
    
    <!-- Sidebar -->
    <div class="w-1/4 min-w-[250px] border-r bg-gray-50 flex flex-col">
        <div class="p-4 border-b bg-white">
            <h3 class="font-bold text-gray-800">Your Conversations</h3>
            <p class="text-xs text-gray-500 mt-1">Talk to your database.</p>
        </div>
        
        <div class="flex-grow overflow-y-auto p-2 space-y-2">
            @foreach($sessions as $chat)
                <a href="{{ route('ai-analytics.chat', ['session' => $chat->id]) }}" 
                   class="block p-3 rounded-lg border {{ request('session') == $chat->id ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-transparent hover:bg-gray-100' }} transition cursor-pointer">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $chat->title }}</p>
                    <span class="text-xs text-gray-400 block mt-1">{{ $chat->updated_at->diffForHumans() }}</span>
                </a>
            @endforeach

            @if($sessions->isEmpty())
                <p class="text-sm text-center text-gray-500 p-4">No recent chats.</p>
            @endif
        </div>
        
        <div class="p-4 border-t bg-white">
            <a href="{{ route('ai-analytics.chat') }}" class="block text-center w-full bg-indigo-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                Start New Chat
            </a>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="w-3/4 flex flex-col relative h-full">
        @php
            $currentSession = request('session') ? $sessions->firstWhere('id', request('session')) : null;
        @endphp

        <!-- Header -->
        <div class="p-4 border-b bg-white flex justify-between items-center z-10">
            <h2 class="font-bold text-gray-800">{{ $currentSession ? 'Current Chat' : 'New Analytical Query' }}</h2>
            <div class="flex items-center gap-4">
                <div class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded font-mono font-medium flex items-center">
                    <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> AI QueryGuard Active
                </div>
                
                @if($currentSession)
                <form action="{{ route('ai-analytics.chat.destroy', $currentSession->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this conversation and all its history?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Delete Conversation">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div class="flex-grow overflow-y-auto p-6 space-y-6" id="messages-container">
            @if($currentSession)
                @foreach($currentSession->messages()->oldest()->get() as $message)
                    <div class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xl {{ $message->role === 'user' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }} p-4 rounded-xl shadow-sm">
                            <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $message->content }}</p>
                            @if($message->role === 'assistant' && config('app.debug'))
                            {{-- We can put debug logic here if we injected SQL later --}}
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div id="hero-welcome" class="h-full flex flex-col items-center justify-center text-center p-8">
                    <div class="bg-indigo-100 p-4 rounded-full mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Ask your database anything</h3>
                    <p class="text-gray-500 mt-2 max-w-md">Query your database using natural language. The AI will generate secure read-only analytical queries over your <a href="{{ route('ai-analytics.data-sources') }}" class="text-indigo-600 underline">Enabled Tables</a>.</p>
                    
                    <div class="mt-8 grid grid-cols-2 gap-4 w-full max-w-lg">
                        <div class="bg-gray-50 border p-3 text-sm text-gray-600 rounded cursor-pointer hover:bg-gray-100 text-left" onclick="document.getElementById('prompt-input').value = this.innerText">How many users registered this month?</div>
                        <div class="bg-gray-50 border p-3 text-sm text-gray-600 rounded cursor-pointer hover:bg-gray-100 text-left" onclick="document.getElementById('prompt-input').value = this.innerText">Show me the top 5 products by combined revenue.</div>
                    </div>
                </div>
            @endif

            <!-- Loading indicator -->
            <div id="loading" class="hidden flex justify-start">
                <div class="bg-gray-100 text-gray-500 p-4 rounded-xl shadow-sm text-sm flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Analyzing database structure and running query...
                </div>
            </div>
        </div>

        <!-- Input Box -->
        <div class="p-4 bg-white border-t rounded-br-xl">
            <form id="chat-form" class="relative">
                <input type="text" id="prompt-input" autocomplete="off" class="w-full border-gray-300 border bg-gray-50 rounded-full py-4 pl-6 pr-16 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" placeholder="Ask about your data..." required>
                <button type="submit" class="absolute right-2 top-2 bottom-2 bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('chat-form');
    const input = document.getElementById('prompt-input');
    const container = document.getElementById('messages-container');
    const loading = document.getElementById('loading');
    
    // Auto-scroll logic
    container.scrollTop = container.scrollHeight;

    let currentSessionId = "{{ $currentSession ? $currentSession->id : '' }}";

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const text = input.value.trim();
        if (!text) return;

        // Clear input
        input.value = '';

        // If this is the first message and the hero is there, hide it
        const hero = document.getElementById('hero-welcome');
        if (hero) hero.style.display = 'none';

        // Add user message visually
        const userDiv = document.createElement('div');
        userDiv.className = 'flex justify-end';
        userDiv.innerHTML = `<div class="max-w-xl bg-indigo-600 text-white p-4 rounded-xl shadow-sm"><p class="text-sm whitespace-pre-wrap leading-relaxed">${text}</p></div>`;
        container.insertBefore(userDiv, loading);
        
        // Show loading
        loading.classList.remove('hidden');
        container.scrollTop = container.scrollHeight;

        let url = currentSessionId 
            ? "{{ route('ai-analytics.chat') }}/" + currentSessionId + "/message" 
            : "{{ route('ai-analytics.chat.store') }}";

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text })
            });
            
            const data = await response.json();
            
            // Hide loading
            loading.classList.add('hidden');

            if (data.status === 'success') {
                if (!currentSessionId && data.chat_id) {
                    currentSessionId = data.chat_id;
                    // Update URL without reloading securely to allow persistence
                    window.history.pushState({}, '', "?session=" + currentSessionId);
                }

                const aiDiv = document.createElement('div');
                aiDiv.className = 'flex justify-start';
                
                let html = `<div class="max-w-xl bg-gray-100 text-gray-800 p-4 rounded-xl shadow-sm">
                        <p class="text-sm whitespace-pre-wrap leading-relaxed">${data.reply}</p>`;
                
                if (data.sql) {
                    html += `<div class="mt-3 p-2 bg-gray-800 text-green-400 font-mono text-[10px] rounded overflow-x-auto"><span class="text-gray-400 block mb-1">Generated Safe SQL:</span>${data.sql}</div>`;
                }

                html += `</div>`;
                aiDiv.innerHTML = html;
                container.insertBefore(aiDiv, loading);
                
            } else {
                alert(data.message || "An error occurred fetching AI inference.");
            }
        } catch (err) {
            loading.classList.add('hidden');
            alert("Network error.");
        }
        
        container.scrollTop = container.scrollHeight;
    });
</script>
@endsection