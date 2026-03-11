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

<div class="mb-6 border-t pt-4">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-md font-bold text-gray-800">Filters (Optional)</h3>
        <button type="button" id="addFilterBtn" class="text-sm bg-indigo-50 text-indigo-600 px-3 py-1 rounded hover:bg-indigo-100 transition">
            + Add Filter
        </button>
    </div>
    <div id="filtersContainer" class="space-y-3">
        <!-- Filters will be injected here -->
    </div>
</div>

<button type="submit" class="bg-indigo-600 text-white w-full py-3 rounded-lg font-bold shadow hover:bg-indigo-700 transition" {{ empty($enabledTables) ? 'disabled' : '' }}>
    {{ isset($card) ? 'Update Dashboard Card' : 'Save Dashboard Card' }}
</button>

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

    // Filters logic
    const existingFilters = @json(old('filters', isset($card) && $card->filters ? $card->filters : []));
    const filtersContainer = document.getElementById('filtersContainer');
    const addFilterBtn = document.getElementById('addFilterBtn');

    function renderFilterRow(filter = { column: '', operator: '=', value: '' }, index) {
        const table = tableSelect.value;
        const columns = table && columnsMap[table] ? columnsMap[table] : [];
        
        const operators = ['=', '!=', '>', '<', '>=', '<=', 'IN', 'NOT IN', 'LIKE', 'IS NULL', 'IS NOT NULL'];

        let colOptions = '<option value="">-- Select Column --</option>';
        columns.forEach(col => {
            colOptions += `<option value="${col}" ${filter.column === col ? 'selected' : ''}>${col}</option>`;
        });

        let opOptions = '';
        operators.forEach(op => {
            opOptions += `<option value="${op}" ${filter.operator === op ? 'selected' : ''}>${op}</option>`;
        });

        const row = document.createElement('div');
        row.className = 'flex gap-2 items-end filter-row';
        row.innerHTML = `
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Column</label>
                <select name="filters[${index}][column]" class="w-full border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 filter-col" required>
                    ${colOptions}
                </select>
            </div>
            <div class="w-32">
                <label class="block text-xs text-gray-500 mb-1">Operator</label>
                <select name="filters[${index}][operator]" class="w-full border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 filter-op">
                    ${opOptions}
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Value</label>
                <input type="text" name="filters[${index}][value]" value="${filter.value || ''}" class="w-full border-gray-300 rounded p-2 text-sm focus:ring-indigo-500 filter-val" placeholder="Value">
            </div>
            <button type="button" class="bg-red-50 text-red-600 px-3 py-2 rounded border hover:bg-red-100 transition remove-filter-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        `;

        // Handle IS NULL / IS NOT NULL toggling
        const opSelect = row.querySelector('.filter-op');
        const valInput = row.querySelector('.filter-val');
        
        opSelect.addEventListener('change', function() {
            if (this.value === 'IS NULL' || this.value === 'IS NOT NULL') {
                valInput.disabled = true;
                valInput.classList.add('bg-gray-100');
            } else {
                valInput.disabled = false;
                valInput.classList.remove('bg-gray-100');
            }
        });
        
        // trigger initial state
        opSelect.dispatchEvent(new Event('change'));

        row.querySelector('.remove-filter-btn').addEventListener('click', () => {
            row.remove();
            reindexFilters();
        });

        filtersContainer.appendChild(row);
    }

    function reindexFilters() {
        const rows = filtersContainer.querySelectorAll('.filter-row');
        rows.forEach((row, index) => {
            row.querySelector('.filter-col').name = `filters[${index}][column]`;
            row.querySelector('.filter-op').name = `filters[${index}][operator]`;
            row.querySelector('.filter-val').name = `filters[${index}][value]`;
        });
    }

    // Refresh filters when table changes
    tableSelect.addEventListener('change', () => {
        // Only reset columns in existing filters, don't delete them.
        const table = tableSelect.value;
        const columns = table && columnsMap[table] ? columnsMap[table] : [];
        const rows = filtersContainer.querySelectorAll('.filter-row');
        
        rows.forEach(row => {
            const colSelect = row.querySelector('.filter-col');
            const currentSelected = colSelect.value;
            let colOptions = '<option value="">-- Select Column --</option>';
            columns.forEach(col => {
                colOptions += `<option value="${col}" ${currentSelected === col ? 'selected' : ''}>${col}</option>`;
            });
            colSelect.innerHTML = colOptions;
        });
    });

    addFilterBtn.addEventListener('click', () => {
        const idx = filtersContainer.querySelectorAll('.filter-row').length;
        renderFilterRow({ column: '', operator: '=', value: '' }, idx);
    });

    // Render initially
    function initFilters() {
        filtersContainer.innerHTML = '';
        if (existingFilters && existingFilters.length > 0) {
            existingFilters.forEach((filter, index) => {
                renderFilterRow(filter, index);
            });
        }
    }
    
    // Only init filters if a table is selected, otherwise we have no columns
    if (tableSelect.value) {
        initFilters();
    }
    
    tableSelect.addEventListener('change', () => {
        if (filtersContainer.children.length === 0) {
            // maybe re-init if empty
        }
    });

</script>
