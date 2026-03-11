@extends('ai-analytics::layout')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Analytics Dashboard</h2>
        <div class="flex gap-2">
            @if($enabledSources == 0)
                <a href="{{ route('ai-analytics.data-sources') }}" class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-100 transition">Enable Data Sources</a>
            @else
                <a href="{{ route('ai-analytics.cards.create') }}" class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-100 transition">Create Card</a>
            @endif
            <a href="{{ route('ai-analytics.chat') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Ask AI</a>
        </div>
    </div>

    @if($enabledSources == 0)
        <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r shadow-sm text-yellow-800 flex justify-between items-center">
            <div>
                <strong>Setup Required:</strong> You haven't enabled any data sources yet.
                <span class="block text-sm opacity-80 mt-1">Visit Data Sources to link your schema to Analytics.</span>
            </div>
            <a href="{{ route('ai-analytics.data-sources') }}" class="text-sm font-bold text-yellow-900 border border-yellow-300 px-3 py-1 rounded bg-yellow-100">Setup Sources</a>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($cardData as $item)
            @php $card = $item['card']; @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col relative group">
                <a href="{{ route('ai-analytics.cards.edit', $card->id) }}" class="absolute top-2 right-2 text-gray-300 hover:text-indigo-600 hidden group-hover:block transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </a>
                
                <span class="text-sm font-medium text-gray-500 mb-1">{{ $card->name }}</span>
                <span class="text-3xl font-bold {{ $item['value'] !== '--' ? 'text-gray-900' : 'text-gray-300' }}">
                    {{ $item['is_currency'] ? '$' : '' }}{{ is_numeric($item['value']) ? number_format($item['value'], str_contains($item['value'], '.') ? 2 : 0) : $item['value'] }}
                </span>
                
                @if($card->description)
                    <p class="text-xs text-gray-400 mt-2">{{ $card->description }}</p>
                @endif
                
                @if($item['error'])
                    <p class="text-xs text-red-500 mt-2 truncate w-full" title="{{ $item['error'] }}">{{ $item['error'] }}</p>
                @endif
                
                <div class="mt-3 text-[10px] text-gray-300 font-mono uppercase tracking-wider">
                    {{ $card->aggregation_type }}({{ $card->column_name ?? '*' }}) on {{ $card->table_name }}
                </div>
            </div>
        @endforeach
    </div>

    @if(empty($cardData) && $enabledSources > 0)
        <div class="bg-white border rounded-xl p-12 text-center shadow-sm">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <h3 class="mt-4 text-gray-800 font-bold text-lg">No Cards Created</h3>
            <p class="text-gray-500 text-sm mt-1 mb-6">Create customizable dashboard widgets to track your metrics.</p>
            <a href="{{ route('ai-analytics.cards.create') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-indigo-700">Create First Card</a>
        </div>
    @endif
    
@endsection