<?php

namespace App\Console\Commands;

use App\Models\MonitoringRequest;
use Illuminate\Console\Command;

class MarkCompletedRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:mark-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark active requests as completed when target date has passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $completedCount = MonitoringRequest::where('status', MonitoringRequest::STATUS_ACTIVE)
            ->where('target_date', '<', now()->toDateString())
            ->update(['status' => MonitoringRequest::STATUS_COMPLETED]);

        $this->info("Marked {$completedCount} request(s) as completed.");

        return Command::SUCCESS;
    }
}
