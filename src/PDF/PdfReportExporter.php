<?php

namespace Mrorko840\AiAnalytics\PDF;

use Barryvdh\DomPDF\Facade\Pdf;
use Mrorko840\AiAnalytics\Contracts\ReportExporterInterface;

class PdfReportExporter implements ReportExporterInterface
{
    public function export(array $reportData): string
    {
        // Load an integrated blade view to render PDF
        $pdf = Pdf::loadView('ai-analytics::reports.pdf', ['report' => $reportData]);
        return $pdf->output();
    }

    public function getExtension(): string
    {
        return 'pdf';
    }

    public function getContentType(): string
    {
        return 'application/pdf';
    }
}
