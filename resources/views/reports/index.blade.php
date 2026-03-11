@extends('ai-analytics::layout')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Generated Reports</h2>

        <form action="{{ route('ai-analytics.reports.store') }}" method="POST" class="flex gap-2">
            @csrf
            <input type="hidden" name="title" value="On-Demand Sync Report">
            <input type="hidden" name="period" value="Last 30 Days">
            <input type="hidden" name="metrics[]" value="revenue">
            <input type="hidden" name="metrics[]" value="signups_count">
            <input type="hidden" name="metrics[]" value="deposits">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">+ Generate
                Report</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title /
                        Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($reports as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $report->title }}</div>
                            <div class="text-sm text-gray-500">{{ $report->report_type ?? 'Standard' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ \Carbon\Carbon::parse($report->payload['period'] ?? now())->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('ai-analytics.reports.show', $report->id) }}"
                                class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                            <div class="inline-flex gap-2">
                                <a href="{{ route('ai-analytics.reports.export', [$report->id, 'pdf']) }}"
                                    class="text-red-500">PDF</a>
                                <a href="{{ route('ai-analytics.reports.export', [$report->id, 'csv']) }}"
                                    class="text-green-600">CSV</a>
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if($reports->isEmpty())
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            No reports generated yet.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="bg-gray-50 p-4 border-t">
            {{ $reports->links() }}
        </div>
    </div>
@endsection