<?php

namespace App\Console\Commands;

use App\Models\MonitoringRequest;
use Illuminate\Console\Command;

class MarkExpiredRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:mark-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark pending verification requests as expired if they are older than 2 hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredCount = MonitoringRequest::where('status', MonitoringRequest::STATUS_PENDING_VERIFICATION)
            ->where('expires_at', '<', now())
            ->update(['status' => MonitoringRequest::STATUS_EXPIRED]);

        $this->info("Marked {$expiredCount} request(s) as expired.");

        return Command::SUCCESS;
    }
}
