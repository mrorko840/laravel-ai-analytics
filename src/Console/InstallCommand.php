<?php

namespace Mrorko840\AiAnalytics\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'ai-analytics:install';
    protected $description = 'Install the AI Analytics package';

    public function handle(): void
    {
        $this->info('Starting AI Analytics Installation...');

        $this->call('vendor:publish', ['--tag' => 'ai-analytics-config']);
        $this->call('vendor:publish', ['--tag' => 'ai-analytics-migrations']);

        $this->info('Configuration and Migrations published.');

        if ($this->confirm('Would you like to run migrations now?', true)) {
            $this->call('migrate');
        }

        $this->info('Setup Complete! Next steps:');
        $this->line('1. Set your AI_ANALYTICS_API_KEY in .env');
        $this->line('2. Configure entity mapping in config/ai-analytics.php');
        $this->line('3. Access the dashboard at /ai-analytics/dashboard');
    }
}
