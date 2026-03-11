<?php

namespace Mrorko840\AiAnalytics\Reports;

use Illuminate\Support\Facades\Storage;
use Mrorko840\AiAnalytics\Contracts\ReportExporterInterface;
use Mrorko840\AiAnalytics\Models\AiAnalyticsReport;
use InvalidArgumentException;

class ReportService
{
    protected ReportBuilder $builder;
    /** @var array<string, ReportExporterInterface> */
    protected array $exporters = [];

    public function __construct(ReportBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function registerExporter(string $format, ReportExporterInterface $exporter): void
    {
        $this->exporters[$format] = $exporter;
    }

    public function getExporter(string $format): ReportExporterInterface
    {
        if (!isset($this->exporters[$format])) {
            throw new InvalidArgumentException("Exporter format [{$format}] is not registered.");
        }
        return $this->exporters[$format];
    }

    public function generateAndSave(
        int $userId,
        string $title,
        string $period,
        array $metrics,
        array $filters = [],
        ?string $type = null
    ): AiAnalyticsReport {
        $reportData = $this->builder->build($title, $period, $metrics, $filters);

        return AiAnalyticsReport::create([
            'user_id' => $userId,
            'title' => $title,
            'report_type' => $type ?? 'custom',
            'filters' => $filters,
            'payload' => $reportData,
        ]);
    }

    public function exportSavedReport(AiAnalyticsReport $report, string $format): string
    {
        $exporter = $this->getExporter($format);
        $content = $exporter->export($report->payload);

        $report->update(['exported_at' => now()]);

        // Optional: Save to disk
        $disk = config('ai-analytics.reports.disk', 'local');
        $path = config('ai-analytics.reports.path', 'ai-analytics/reports');
        $filename = "{$path}/report_{$report->id}.{$exporter->getExtension()}";

        Storage::disk($disk)->put($filename, $content);

        return $content;
    }
}
