@extends('ai-analytics::layout')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('ai-analytics.reports') }}" class="text-sm text-blue-600 mb-2 inline-block">&larr; Back to
                Reports</a>
            <h2 class="text-3xl font-bold text-gray-800">{{ $report->title }}</h2>
            <p class="text-gray-500">Period: {{ $report->payload['period'] ?? 'N/A' }} | Generated:
                {{ $report->created_at->format('F j, Y, g:i a') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('ai-analytics.reports.export', [$report->id, 'pdf']) }}"
                class="bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-100">Export
                PDF</a>
            <a href="{{ route('ai-analytics.reports.export', [$report->id, 'csv']) }}"
                class="bg-green-50 text-green-700 border border-green-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-100">Export
                CSV</a>
        </div>
    </div>

    @if(!empty($report->payload['insights']))
        <div class="mb-8 bg-blue-50 border border-blue-100 rounded-xl p-6">
            <h3 class="text-lg font-bold text-blue-900 mb-2 flex items-center">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                AI Analyst Insights
            </h3>
            <div class="text-blue-800 prose text-sm max-w-none">
                {!! nl2br(e($report->payload['insights'])) !!}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($report->payload['metrics'] ?? [] as $key => $metric)
            @if(!is_array($metric['data']['value'] ?? null))
                <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
                    <span class="block text-sm font-medium text-gray-500 mb-2">{{ $metric['label'] }}</span>
                    <span class="block text-4xl font-extrabold text-gray-900">
                        @if(str_contains(strtolower($metric['label']), 'revenue') || str_contains(strtolower($metric['label']), 'deposit'))
                            ${{ number_format((float) ($metric['data']['value'] ?? 0), 2) }}
                        @else
                            {{ $metric['data']['value'] ?? 0 }}
                        @endif
                    </span>
                </div>
            @endif
        @endforeach
    </div>

    @foreach($report->payload['metrics'] ?? [] as $key => $metric)
        @if(is_array($metric['data']['value'] ?? null))
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ $metric['label'] }} Details</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                @if(!empty($metric['data']['value']))
                                    @foreach(array_keys((array) current($metric['data']['value'])) as $th)
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">{{ $th }}</th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($metric['data']['value'] as $row)
                                <tr>
                                    @foreach((array) $row as $cell)
                                        <td class="px-4 py-2 text-gray-900">{{ is_array($cell) ? json_encode($cell) : $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach

    @if(!empty($report->payload['tabular_data']) && is_array($report->payload['tabular_data']) && count($report->payload['tabular_data']) > 0)
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Generated SQL Table Data</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm border">
                    <thead class="bg-indigo-50 border-b">
                        <tr>
                            @foreach(array_keys((array) current($report->payload['tabular_data'])) as $th)
                                <th class="px-4 py-3 text-left font-bold text-indigo-800 uppercase tracking-wide">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($report->payload['tabular_data'] as $row)
                            <tr class="hover:bg-gray-50">
                                @foreach((array) $row as $cell)
                                    <td class="px-4 py-3 text-gray-800">{{ is_scalar($cell) || is_null($cell) ? $cell : json_encode($cell) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection