@extends('ai-analytics::layout')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Analytics Overview</h2>
        <a href="{{ route('ai-analytics.chat') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Ask
            AI</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach(['signups_count' => 'Signups', 'revenue' => 'Revenue', 'deposits' => 'Deposits', 'withdrawals' => 'Withdrawals'] as $key => $label)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 mb-1">{{ $label }}</span>
                <span class="text-3xl font-bold text-gray-900">
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
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Product
                            </th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Sales</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-3">Revenue
                            </th>
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
                        @else
                            <tr>
                                <td colspan="3" class="py-3 text-sm text-gray-500">No data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Conversion Rate</h3>
            <div class="flex items-center justify-center h-48">
                <div class="text-center">
                    <span class="text-5xl font-extrabold text-indigo-600">
                        @if(isset($metrics['conversion_rate']))
                            {{ $metrics['conversion_rate']['value'] }}%
                        @else
                            --
                        @endif
                    </span>
                    <p class="text-gray-500 mt-2">Signups to Purchases</p>
                </div>
            </div>
        </div>
    </div>
@endsection