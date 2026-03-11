<?php

namespace Mrorko840\AiAnalytics\Contracts;

interface ReportExporterInterface
{
    /**
     * Export the finalized report.
     *
     * @param array $reportData Normalized report data array
     * @return string The exported content (PDF blob, CSV plain text, JSON string, etc.)
     */
    public function export(array $reportData): string;

    /**
     * Get the default file extension for this exporter (e.g. 'pdf', 'csv')
     */
    public function getExtension(): string;

    /**
     * Get the associated MIME type (e.g. 'application/pdf')
     */
    public function getContentType(): string;
}
