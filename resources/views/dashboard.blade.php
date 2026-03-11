@extends('ai-analytics::layout')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Analytics Overview</h2>
        <div class="flex gap-2">
            <a href="{{ route('ai-analytics.data-sources') }}" class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-100 transition">Data Sources Setup</a>
            <a href="{{ route('ai-analytics.chat') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Ask AI</a>
        </div>
    </div>

    <!-- Warnings for missing Mappings -->
    @if(count(array_filter($errors)) > 0)
        <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r shadow-sm text-yellow-800 flex justify-between items-center">
            <div>
                <strong>Setup Required:</strong> Some metrics are missing required database mappings. 
                <span class="block text-sm opacity-80 mt-1">Visit Data Sources to link your schema to Analytics.</span>
            </div>
            <a href="{{ route('ai-analytics.diagnostics') }}" class="text-sm font-bold text-yellow-900 border border-yellow-300 px-3 py-1 rounded bg-yellow-100">View Diagnostics</a>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach(['signups_count' => 'Signups', 'revenue' => 'Revenue', 'deposits' => 'Deposits', 'withdrawals' => 'Withdrawals'] as $key => $label)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 mb-1">{{ $label }}</span>
                <span class="text-3xl font-bold {{ isset($metrics[$key]) ? 'text-gray-900' : 'text-gray-300' }}">
                    @if(isset($metrics[$key]))
                        @if(is_array($metrics[$key]['value']))
                            {{ json_encode($metrics[$key]['value']) }}
                        @else
                            {{ $key === 'revenue' || $key === 'deposits' || $key === 'withdrawals' ? '$' : '' }}{{ number_format($metrics[$key]['value'], 2) }}
                        @endif
                    @else
                        --
                    @endif
                </span>
                @if(isset($errors[$key]))
                    <p class="text-xs text-red-500 mt-2 truncate max-w-full" title="{{ $errors[$key] }}">Incomplete config</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Top Selling Products</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Product</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Sales</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(isset($metrics['top_selling_products']))
                            @foreach($metrics['top_selling_products']['value'] as $product)
                            <tr>
                                <td class="py-3 text-sm text-gray-900">{{ $product['name'] }}</td>
                                <td class="py-3 text-sm text-gray-500">{{ $product['sales'] }}</td>
                                <td class="py-3 text-sm text-gray-500">${{ number_format($product['revenue'], 2) }}</td>
                            </tr>
                            @endforeach
                            @if(count($metrics['top_selling_products']['value']) === 0)
                                <tr><td colspan="3" class="py-4 text-center text-sm text-gray-500">No sales recorded in period.</td></tr>
                            @endif
                        @else
                            <tr>
                                <td colspan="3" class="py-8 text-center bg-gray-50 rounded text-sm text-gray-500">
                                    <span class="block font-medium mb-1 text-gray-700">Mapping Incomplete</span>
                                    <span class="text-xs">Configure Order, Product, & Order Item mappings to enable this widget.</span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Conversion Rate</h3>
            <div class="flex items-center justify-center h-48 bg-gray-50 rounded border border-gray-100">
                <div class="text-center">
                    <span class="text-5xl font-extrabold {{ isset($metrics['conversion_rate']) ? 'text-indigo-600' : 'text-gray-300' }}">
                        @if(isset($metrics['conversion_rate']))
                            {{ $metrics['conversion_rate']['value'] }}%
                        @else
                            --%
                        @endif
                    </span>
                    <p class="text-gray-500 mt-2">Signups to Purchases</p>
                    @if(isset($errors['conversion_rate']))
                        <p class="text-xs text-red-500 mt-2">{{ $errors['conversion_rate'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection