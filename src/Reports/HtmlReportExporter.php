<?php

namespace Mrorko840\AiAnalytics\Reports;

use Illuminate\Support\Facades\View;
use Mrorko840\AiAnalytics\Contracts\ReportExporterInterface;

class HtmlReportExporter implements ReportExporterInterface
{
    public function export(array $reportData): string
    {
        return View::make('ai-analytics::reports.html', ['report' => $reportData])->render();
    }

    public function getExtension(): string
    {
        return 'html';
    }

    public function getContentType(): string
    {
        return 'text/html';
    }
}
