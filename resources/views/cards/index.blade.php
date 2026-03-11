@extends('ai-analytics::layout')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dynamic Dashboard Cards</h2>
        <p class="text-xs text-gray-500 mt-1">Manage the metrics shown on your main analytics dashboard.</p>
    </div>
    <a href="{{ route('ai-analytics.cards.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">Create Card</a>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Card Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table Source</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aggregation</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($cards as $card)
            <tr>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $card->name }}</div>
                    <div class="text-xs text-gray-500">{{ $card->description }}</div>
                </td>
                <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $card->table_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    <span class="bg-gray-100 px-2 py-1 rounded font-bold text-gray-700">{{ $card->aggregation_type }}</span>
                    @if($card->column_name)
                    on <span class="font-mono text-xs">{{ $card->column_name }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('ai-analytics.cards.edit', $card->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                    <form action="{{ route('ai-analytics.cards.destroy', $card->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this card?');">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
            
            @if($cards->isEmpty())
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-gray-500">No custom dashboard cards created yet.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
