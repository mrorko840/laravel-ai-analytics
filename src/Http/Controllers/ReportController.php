<?php

namespace Mrorko840\AiAnalytics\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Mrorko840\AiAnalytics\Models\AiAnalyticsReport;
use Mrorko840\AiAnalytics\Reports\ReportService;
use Exception;

class ReportController extends Controller
{
    protected ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $reports = AiAnalyticsReport::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('ai-analytics::reports.index', compact('reports'));
    }

    public function show($id)
    {
        $report = AiAnalyticsReport::findOrFail($id);

        // Ensure user can view report
        if ($report->user_id && $report->user_id !== auth()->id()) {
            abort(403);
        }

        return view('ai-analytics::reports.show', compact('report'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'period' => 'required|string',
            'metrics' => 'required|array',
        ]);

        $report = $this->service->generateAndSave(
            auth()->id() ?? 0,
            $request->input('title'),
            $request->input('period'),
            $request->input('metrics'),
            $request->input('filters', []),
            $request->input('type', 'user_generated')
        );

        return redirect()->route('ai-analytics.reports.show', $report->id);
    }

    public function export(Request $request, $id, $format)
    {
        $report = AiAnalyticsReport::findOrFail($id);

        if ($report->user_id && $report->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $content = $this->service->exportSavedReport($report, $format);
            $exporter = $this->service->getExporter($format);

            return response($content)
                ->header('Content-Type', $exporter->getContentType())
                ->header('Content-Disposition', 'attachment; filename="report_' . $id . '.' . $exporter->getExtension() . '"');

        } catch (Exception $e) {
            return back()->withError("Failed to export: " . $e->getMessage());
        }
    }
}
