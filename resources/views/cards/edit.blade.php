@extends('ai-analytics::layout')

@section('content')
<div class="max-w-3xl mx-auto mt-8">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Edit Dashboard Card</h2>
        <a href="{{ route('ai-analytics.dashboard') }}" class="text-sm text-indigo-600 hover:underline">Back to Dashboard</a>
    </div>

    @if(empty($enabledTables))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-yellow-800">
            <strong>No data sources enabled!</strong> You must enable at least one table in <a href="{{ route('ai-analytics.data-sources') }}" class="underline">Data Sources</a> before editing a card.
        </div>
    @endif

    <form action="{{ route('ai-analytics.cards.update', $card->id) }}" method="POST" class="bg-white rounded-xl shadow-sm border p-6">
        @csrf
        @method('PUT')
        @include('ai-analytics::cards._form')
    </form>
</div>
@endsection
