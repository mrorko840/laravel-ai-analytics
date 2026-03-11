@extends('ai-analytics::layout')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Dynamic Data Sources</h2>
    <a href="{{ route('ai-analytics.diagnostics') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold border hover:bg-gray-200 transition">View Diagnostics</a>
</div>

<p class="text-sm text-gray-500 mb-6">Explore your current database connection and select the tables that containing business entities (Users, Orders, Transactions) to map them to AI Analytics.</p>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($tables as $table)
        @php
            $mapping = $mappings->firstWhere('table_name', $table);
        @endphp
        <div class="bg-white rounded-xl shadow-sm border {{ $mapping ? 'border-indigo-300' : 'border-gray-200' }} p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-gray-800 mb-2 truncate" title="{{ $table }}">
                    {{ $table }}
                </h3>
                @if($mapping)
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded font-medium mb-4">
                        Mapped as: {{ strtoupper($mapping->entity_name) }}
                    </span>
                @else
                    <span class="inline-block bg-gray-100 text-gray-500 text-xs px-2 py-1 rounded font-medium mb-4">
                        Unmapped Table
                    </span>
                @endif
            </div>
            
            <a href="{{ route('ai-analytics.data-sources.tables', $table) }}" class="w-full text-center {{ $mapping ? 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }} border py-2 rounded-lg text-sm font-medium transition">
                Inspect & Map Columns
            </a>
        </div>
    @endforeach
</div>
@endsection
