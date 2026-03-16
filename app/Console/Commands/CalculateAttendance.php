<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateAttendance extends Command
{
    protected $signature = 'hr:calculate-attendance';
    protected $description = 'Auto-generated command stub for CalculateAttendance';

    public function handle(): int
    {
        $this->info('CalculateAttendance executed successfully.');

        return self::SUCCESS;
    }
}
