<?php

namespace App\Console\Commands;

use App\Models\MonitoringRequest;
use App\Models\NotificationPreference;
use App\Mail\DailySummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailySummaries extends Command
{
    protected $signature = 'notifications:send-daily-summaries';
    protected $description = 'Send daily summary emails to users with active weather readings';

    public function handle()
    {
        $this->info('Sending daily summaries...');

        // Get all notification preferences with daily_summary enabled
        $preferences = NotificationPreference::where('daily_summary_enabled', true)->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($preferences as $pref) {
            // Get active requests for this user/email
            $requests = $pref->user_id
                ? MonitoringRequest::where('user_id', $pref->user_id)
                    ->where('status', MonitoringRequest::STATUS_ACTIVE)
                    ->where('notifications_enabled', true)
                    ->get()
                : MonitoringRequest::where('email', $pref->email)
                    ->where('status', MonitoringRequest::STATUS_ACTIVE)
                    ->where('notifications_enabled', true)
                    ->get();

            // Skip if no active requests
            if ($requests->isEmpty()) {
                $skippedCount++;
                continue;
            }

            // Get recipient email
            $recipientEmail = $pref->user_id
                ? $pref->user->email
                : $pref->email;

            try {
                Mail::to($recipientEmail)->send(
                    new DailySummary($requests, $recipientEmail, $pref->token)
                );
                $this->info("✓ Sent daily summary to {$recipientEmail} ({$requests->count()} requests)");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$recipientEmail}: {$e->getMessage()}");
            }
        }

        $this->info("\nCompleted: {$sentCount} sent, {$skippedCount} skipped (no active requests)");

        return 0;
    }
}
