@extends('ai-analytics::layout')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Data Sources</h2>
    <a href="{{ route('ai-analytics.diagnostics') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold border hover:bg-gray-200 transition">System Diagnostics</a>
</div>

<p class="text-sm text-gray-500 mb-6">Explore the database tables detected in your application. Enable the tables you want the AI Analytics Assistant to use for querying, generating reports, and tracking cards.</p>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($tableDetails as $table => $details)
        <div class="bg-white rounded-xl shadow-sm border {{ $details['is_enabled'] ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-200' }} p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-gray-800 truncate" title="{{ $table }}">{{ $table }}</h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer table-toggle" data-table="{{ $table }}" {{ $details['is_enabled'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-sm font-medium {{ $details['is_enabled'] ? 'text-indigo-600' : 'text-gray-400' }} toggle-label">{{ $details['is_enabled'] ? 'Enabled' : 'Disabled' }}</span>
                    </label>
                </div>
                
                <p class="text-xs text-gray-500 mb-3">{{ count($details['columns']) }} columns discovered</p>
                <div class="max-h-24 overflow-y-auto mb-4 bg-gray-50 p-2 rounded text-xs font-mono text-gray-600">
                    {{ implode(', ', $details['columns']) }}
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    document.querySelectorAll('.table-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const table = this.getAttribute('data-table');
            const isEnabled = this.checked;
            const label = this.nextElementSibling.nextElementSibling;
            const container = this.closest('.bg-white');

            if (isEnabled) {
                label.textContent = 'Enabled';
                label.classList.remove('text-gray-400');
                label.classList.add('text-indigo-600');
                container.classList.add('border-indigo-500', 'ring-1', 'ring-indigo-500');
                container.classList.remove('border-gray-200');
            } else {
                label.textContent = 'Disabled';
                label.classList.remove('text-indigo-600');
                label.classList.add('text-gray-400');
                container.classList.remove('border-indigo-500', 'ring-1', 'ring-indigo-500');
                container.classList.add('border-gray-200');
            }

            fetch('{{ route("ai-analytics.data-sources.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    table_name: table,
                    is_enabled: isEnabled
                })
            }).catch(error => {
                console.error('Error toggling table:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
</script>
@endsection
