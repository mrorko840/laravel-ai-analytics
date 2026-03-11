@extends('ai-analytics::layout')

@section('content')
<div class="max-w-3xl mx-auto mt-8">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">{{ isset($card) ? 'Edit Card' : 'Create Custom Dashboard Card' }}</h2>
        <a href="{{ route('ai-analytics.dashboard') }}" class="text-sm text-indigo-600 hover:underline">Back to Dashboard</a>
    </div>

    @if(empty($enabledTables))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-yellow-800">
            <strong>No data sources enabled!</strong> You must enable at least one table in <a href="{{ route('ai-analytics.data-sources') }}" class="underline">Data Sources</a> before creating a card.
        </div>
    @endif

    <form action="{{ isset($card) ? route('ai-analytics.cards.update', $card->id) : route('ai-analytics.cards.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border p-6">
        @csrf
        @if(isset($card))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">Card Title</label>
            <input type="text" name="name" value="{{ old('name', $card->name ?? '') }}" class="w-full border-gray-300 rounded p-2 focus:ring-indigo-500" required placeholder="E.g. Total Active Users, Total Deposits">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">Description (Optional)</label>
            <input type="text" name="description" value="{{ old('description', $card->description ?? '') }}" class="w-full border-gray-300 rounded p-2 focus:ring-indigo-500" placeholder="Small subtitle under the number">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Target Table</label>
                <select name="table_name" id="tableSelect" class="w-full border-gray-300 rounded p-2 focus:ring-indigo-500" required>
                    <option value="">-- Choose enabled table --</option>
                    @foreach($enabledTables as $table)
                        <option value="{{ $table }}" {{ old('table_name', $card->table_name ?? '') === $table ? 'selected' : '' }}>{{ $table }}</option>
                    @endforeach
                </select>
                @error('table_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Aggregation Function</label>
                <select name="aggregation_type" id="aggSelect" class="w-full border-gray-300 rounded p-2 focus:ring-indigo-500" required>
                    @foreach(['COUNT', 'SUM', 'AVG', 'MAX', 'MIN'] as $agg)
                        <option value="{{ $agg }}" {{ old('aggregation_type', $card->aggregation_type ?? 'COUNT') === $agg ? 'selected' : '' }}>{{ $agg }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Target Column (Optional for COUNT, Required for SUM/AVG)</label>
            <select name="column_name" id="columnSelect" class="w-full border-gray-300 rounded p-2 focus:ring-indigo-500">
                <option value="">-- Apply Aggregation to Column --</option>
            </select>
            <p class="text-xs text-gray-500 mt-1" id="columnHelp">If counting the whole table, you can leave this blank.</p>
        </div>

        <button type="submit" class="bg-indigo-600 text-white w-full py-3 rounded-lg font-bold shadow hover:bg-indigo-700 transition" {{ empty($enabledTables) ? 'disabled' : '' }}>
            {{ isset($card) ? 'Update Dashboard Card' : 'Save Dashboard Card' }}
        </button>
    </form>
</div>

<script>
    const columnsMap = @json($tableColumns);
    const tableSelect = document.getElementById('tableSelect');
    const columnSelect = document.getElementById('columnSelect');
    const aggSelect = document.getElementById('aggSelect');
    const selectedCol = "{{ old('column_name', $card->column_name ?? '') }}";

    function updateColumns() {
        const table = tableSelect.value;
        columnSelect.innerHTML = '<option value="">-- Apply Aggregation to Column --</option>';
        
        if (table && columnsMap[table] && aggSelect.value !== 'COUNT') {
            columnSelect.required = true;
        } else {
            columnSelect.required = false;
        }

        if (table && columnsMap[table]) {
            columnsMap[table].forEach(col => {
                const opt = document.createElement('option');
                opt.value = col;
                opt.textContent = col;
                if (col === selectedCol) opt.selected = true;
                columnSelect.appendChild(opt);
            });
        }
    }

    tableSelect.addEventListener('change', updateColumns);
    aggSelect.addEventListener('change', updateColumns);

    // Initial load
    if (tableSelect.value) {
        updateColumns();
    }
</script>
@endsection
