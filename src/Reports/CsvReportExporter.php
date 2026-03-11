<?php

namespace Mrorko840\AiAnalytics\Reports;

use Mrorko840\AiAnalytics\Contracts\ReportExporterInterface;

class CsvReportExporter implements ReportExporterInterface
{
    public function export(array $reportData): string
    {
        $output = fopen('php://temp', 'r+');
        $delimiter = config('ai-analytics.exports.csv_delimiter', ',');

        // CSV Header
        fputcsv($output, ['Metric', 'Value', 'From', 'To'], $delimiter);

        foreach ($reportData['metrics'] as $key => $metric) {
            $data = $metric['data'];
            if (is_array($data)) {
                $val = is_array($data['value']) ? json_encode($data['value']) : $data['value'];
                fputcsv($output, [
                    $metric['label'],
                    $val,
                    $data['from'] ?? '',
                    $data['to'] ?? '',
                ], $delimiter);
            }
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    public function getExtension(): string
    {
        return 'csv';
    }

    public function getContentType(): string
    {
        return 'text/csv';
    }
}
