<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendInvoices extends Command
{
    protected $signature = 'sales:send-invoices';
    protected $description = 'Auto-generated command stub for SendInvoices';

    public function handle(): int
    {
        $this->info('SendInvoices executed successfully.');

        return self::SUCCESS;
    }
}
