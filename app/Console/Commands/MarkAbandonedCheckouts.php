<?php

namespace App\Console\Commands;

use App\Model\PendingCheckout;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkAbandonedCheckouts extends Command
{
    protected $signature = 'pending-checkouts:mark-abandoned';
    protected $description = 'Delete pending checkouts older than 24 hours';

    public function handle()
    {
        $count = PendingCheckout::where('created_at', '<', Carbon::now()->subHours(24))
            ->delete();

        $this->info("{$count} pending checkouts deleted.");
    }
}
