@extends('ai-analytics::layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('ai-analytics.data-sources') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Tables</a>
    <h2 class="text-2xl font-bold text-gray-800 mt-2">Map Table: <span class="bg-gray-100 px-2 py-1 rounded">{{ $table }}</span></h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Available Columns</h3>
            <ul class="text-sm text-gray-600 bg-gray-50 rounded border p-4 max-h-96 overflow-y-auto">
                @foreach($columns as $col)
                    <li class="py-1 border-b border-gray-100 last:border-0 font-mono">{{ $col }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    
    <div class="lg:col-span-2">
        <form action="{{ route('ai-analytics.data-sources.mappings.save') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            @csrf
            <input type="hidden" name="table_name" value="{{ $table }}">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 text-green-700 border-l-4 border-green-500 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Assign Semantic Entity Type</label>
                <select name="entity_name" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 p-3" required id="entitySelect">
                    <option value="">-- Select Semantic Role --</option>
                    @foreach($entities as $ent)
                        <option value="{{ $ent }}">{{ ucfirst(str_replace('_', ' ', $ent)) }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">E.g. Assign your 'users' table to the 'User' semantic entity type.</p>
            </div>

            <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Column Mapping Definitions</h4>
            <div class="grid gap-4 mb-6" id="mappingFields">
                <p class="text-sm text-gray-500 italic">Select an entity type above to see configurable mappings.</p>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700 shadow-sm transition w-full">
                Save Tracking Mapping
            </button>
        </form>
    </div>
</div>

<script>
    const columns = @json($columns);
    const select = document.getElementById('entitySelect');
    const container = document.getElementById('mappingFields');

    function createSelect(name, label, required = false) {
        let html = `<div class="bg-gray-50 p-3 rounded border">
            <label class="block text-sm font-semibold text-gray-700 mb-1">${label}</label>
            <select name="mapping[${name}]" class="w-full border-gray-300 rounded focus:ring-indigo-500 p-2 text-sm" ${required ? 'required' : ''}>
                <option value="">-- No mapping --</option>`;
        columns.forEach(col => {
            html += `<option value="${col}">${col}</option>`;
        });
        html += `</select></div>`;
        return html;
    }

    function createInput(name, label, placeholder = '') {
        return `<div class="bg-gray-50 p-3 rounded border">
            <label class="block text-sm font-semibold text-gray-700 mb-1">${label}</label>
            <input type="text" name="mapping[${name}]" class="w-full border-gray-300 rounded p-2 text-sm" placeholder="${placeholder}">
        </div>`;
    }

    select.addEventListener('change', (e) => {
        let val = e.target.value;
        let html = '';

        if (val === 'user') {
            html += createSelect('id_column', 'Primary ID Column', true);
            html += createSelect('created_at_column', 'Registered At Column (Date)');
        } 
        else if (val === 'order') {
            html += createSelect('id_column', 'Order ID Column', true);
            html += createSelect('created_at_column', 'Created At Column (Date)');
            html += createSelect('amount_column', 'Grand Total / Amount Column');
            html += createSelect('status_column', 'Status Column');
            html += createSelect('user_foreign_key', 'User ID Column');
            html += createInput('paid_statuses', 'Paid Statuses (comma separated)', 'paid,completed,shipped');
        }
        else if (val === 'order_item') {
            html += createSelect('order_foreign_key', 'Order ID Column');
            html += createSelect('product_foreign_key', 'Product ID Column');
            html += createSelect('quantity_column', 'Quantity Column');
            html += createSelect('price_column', 'Price / Amount Column');
        }
        else if (val === 'product') {
            html += createSelect('id_column', 'Product ID', true);
            html += createSelect('name_column', 'Product Name / Title');
            html += createSelect('price_column', 'Price Column');
        }
        else if (val === 'transaction') {
            html += createSelect('created_at_column', 'Created At');
            html += createSelect('amount_column', 'Amount Column');
            html += createSelect('type_column', 'Transaction Type Column');
            html += createInput('deposit_value', 'Value for Deposit type', 'deposit');
            html += createInput('withdrawal_value', 'Value for Withdrawal type', 'withdrawal');
            html += createSelect('status_column', 'Status Column');
            html += createInput('success_statuses', 'Success Statuses (comma separated)', 'complete,success');
        }
        else {
            html += `<p class="text-sm text-gray-500 italic">Select an entity type above to see configurable mappings.</p>`;
        }

        container.innerHTML = html;
    });
</script>
@endsection
