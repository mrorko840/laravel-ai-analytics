@extends('ai-analytics::layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('ai-analytics.data-sources') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Sources</a>
    <h2 class="text-2xl font-bold text-gray-800 mt-2">System Diagnostics</h2>
    <p class="text-sm text-gray-500 mt-1">Review your AI Analytics integration health and mapping completion.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">

    <div>
        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Required Entity Mappings</h3>
        <ul class="space-y-3">
            @foreach($diagnostics as $entity => $data)
            <li class="bg-white rounded-lg shadow-sm border p-4 flex items-start justify-between">
                <div>
                    <h4 class="font-bold text-gray-700 capitalize">{{ str_replace('_', ' ', $entity) }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ $data['message'] }}</p>
                </div>
                @if($data['status'] === 'ok')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Missing</span>
                @endif
            </li>
            @endforeach
        </ul>
    </div>

    <div>
        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Core Health Status</h3>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <dl class="space-y-4 text-sm max-w-none text-gray-700">
                <div class="flex justify-between pb-2 border-b">
                    <dt class="font-medium text-gray-500">Database Connection</dt>
                    <dd class="font-bold">{{ $health['connection'] }}</dd>
                </div>
                
                <div class="flex justify-between pb-2 border-b">
                    <dt class="font-medium text-gray-500">Analytics AI Provider</dt>
                    <dd class="font-bold uppercase">{{ $health['ai_provider'] }}</dd>
                </div>

                <div class="flex justify-between pb-2 border-b">
                    <dt class="font-medium text-gray-500">System Ready</dt>
                    <dd class="font-bold">
                        @if($health['metrics_ready'])
                            <span class="text-green-600">Yes - Fully Mapped</span>
                        @else
                            <span class="text-red-600">No - Incomplete Mappings</span>
                        @endif
                    </dd>
                </div>

                <div class="flex justify-between pb-2">
                    <dt class="font-medium text-gray-500">Debug Mode</dt>
                    <dd class="font-bold {{ $health['app_debug'] ? 'text-yellow-600' : 'text-green-600' }}">
                        {{ $health['app_debug'] ? 'Enabled' : 'Disabled' }}
                    </dd>
                </div>
            </dl>
        </div>
        
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded p-4">
            <h4 class="font-bold text-blue-800 mb-1">What happens if a mapping is missing?</h4>
            <p class="text-xs text-blue-700">The metrics utilizing that entity will fail gracefully inside the Dashboard and Chat, showing a friendly diagnostic error instead of crashing the system. AI Analytics never attempts to read undefined tables directly.</p>
        </div>
    </div>

</div>
@endsection
