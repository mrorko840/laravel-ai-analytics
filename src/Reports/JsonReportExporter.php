<?php

namespace Mrorko840\AiAnalytics\Reports;

use Mrorko840\AiAnalytics\Contracts\ReportExporterInterface;

class JsonReportExporter implements ReportExporterInterface
{
    public function export(array $reportData): string
    {
        return json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function getExtension(): string
    {
        return 'json';
    }

    public function getContentType(): string
    {
        return 'application/json';
    }
}
