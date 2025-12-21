<?php

namespace App\Console\Commands;

use App\Models\MonitoringRequest;
use App\Models\NotificationPreference;
use App\Mail\FinalSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendFinalSummaries extends Command
{
    protected $signature = 'notifications:send-final-summaries';
    protected $description = 'Send final summary emails for completed weather readings';

    public function handle()
    {
        $this->info('Sending final summaries...');

        // Get requests that were marked as completed today
        $completedRequests = MonitoringRequest::where('status', MonitoringRequest::STATUS_COMPLETED)
            ->where('notifications_enabled', true)
            ->whereDate('updated_at', today())
            ->get();

        if ($completedRequests->isEmpty()) {
            $this->info('No newly completed requests found.');
            return 0;
        }

        $this->info("Found {$completedRequests->count()} newly completed requests");

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($completedRequests as $request) {
            // Get notification preferences
            $preferences = $request->user_id
                ? NotificationPreference::getForUser($request->user_id)
                : NotificationPreference::getForEmail($request->email);

            // Check if final summary notifications are enabled
            if (!$preferences->final_summary_enabled) {
                $this->info("⊘ Skipped {$request->location} - final summaries disabled");
                $skippedCount++;
                continue;
            }

            // Get recipient email
            $recipientEmail = $request->user_id
                ? $request->user->email
                : $request->email;

            try {
                Mail::to($recipientEmail)->send(
                    new FinalSummary($request, $preferences->token)
                );
                $this->info("✓ Sent final summary to {$recipientEmail} for {$request->location}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$recipientEmail}: {$e->getMessage()}");
            }
        }

        $this->info("\nCompleted: {$sentCount} sent, {$skippedCount} skipped");

        return 0;
    }
}
