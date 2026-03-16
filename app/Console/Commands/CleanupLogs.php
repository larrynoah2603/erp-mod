<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupLogs extends Command
{
    protected $signature = 'core:cleanup-logs';
    protected $description = 'Auto-generated command stub for CleanupLogs';

    public function handle(): int
    {
        $this->info('CleanupLogs executed successfully.');

        return self::SUCCESS;
    }
}
