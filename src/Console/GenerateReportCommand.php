<?php

namespace Mrorko840\AiAnalytics\Console;

use Illuminate\Console\Command;
use Mrorko840\AiAnalytics\Reports\ReportService;

class GenerateReportCommand extends Command
{
    protected $signature = 'ai-analytics:report:generate {title} {--period=30-days}';
    protected $description = 'Generate a background report';

    public function handle(ReportService $service): void
    {
        $this->info('Generating background report...');

        $metrics = ['signups_count', 'revenue', 'withdrawals'];

        $report = $service->generateAndSave(
            0,
            $this->argument('title'),
            $this->option('period'),
            $metrics,
            [],
            'scheduled'
        );

        $this->info("Report generated with ID: {$report->id}");
    }
}
