<?php

namespace App\Console\Commands;

use App\Models\MonitoringRequest;
use App\Models\NotificationPreference;
use App\Mail\FinalSummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestFinalSummaries extends Command
{
    protected $signature = 'test:final-summaries {request_id? : Monitoring request ID to test with}';
    protected $description = 'Test final summary email sending (for debugging)';

    public function handle()
    {
        $requestId = $this->argument('request_id');

        if (!$requestId) {
            // Show all completed requests
            $completedRequests = MonitoringRequest::where('status', MonitoringRequest::STATUS_COMPLETED)
                ->where('notifications_enabled', true)
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get();

            if ($completedRequests->isEmpty()) {
                $this->error('No completed monitoring requests with notifications enabled!');
                $this->info('Create a test request or mark one as completed.');
                return 1;
            }

            $this->info('Recent completed requests:');
            $this->table(
                ['ID', 'Location', 'Email/User', 'Target Date', 'Completed'],
                $completedRequests->map(fn($r) => [
                    $r->id,
                    $r->location,
                    $r->user_id ? "User #{$r->user_id}" : $r->email,
                    $r->target_date->format('Y-m-d'),
                    $r->updated_at->diffForHumans(),
                ])
            );

            $requestId = $this->ask('Enter request ID to test with');
            if (!$requestId) {
                return 1;
            }
        }

        $request = MonitoringRequest::find($requestId);

        if (!$request) {
            $this->error("Monitoring request #{$requestId} not found!");
            return 1;
        }

        if (!$request->notifications_enabled) {
            $this->warn('Notifications are DISABLED for this request!');
            if (!$this->confirm('Send anyway for testing?')) {
                return 1;
            }
        }

        // Get notification preferences
        $preferences = $request->user_id
            ? NotificationPreference::getForUser($request->user_id)
            : NotificationPreference::getForEmail($request->email);

        if (!$preferences->final_summary_enabled) {
            $this->warn('Final summary is DISABLED for this user!');
            if (!$this->confirm('Send anyway for testing?')) {
                return 1;
            }
        }

        $recipientEmail = $request->user_id ? $request->user->email : $request->email;

        $this->info("Sending final summary to: {$recipientEmail}");
        $this->info("Request: {$request->location} on {$request->target_date->format('Y-m-d')}");

        // Check if there are any snapshots
        $snapshotCount = $request->forecastSnapshots()->count();
        if ($snapshotCount === 0) {
            $this->warn('No forecast snapshots found for this request!');
            if (!$this->confirm('Send anyway? (Email will be empty)')) {
                return 1;
            }
        } else {
            $this->info("Found {$snapshotCount} forecast snapshot(s)");
        }

        try {
            Mail::to($recipientEmail)->send(
                new FinalSummary($request, $preferences->token)
            );
            $this->info("✓ Email sent successfully!");
            $this->info("Check your inbox at: {$recipientEmail}");
        } catch (\Exception $e) {
            $this->error("✗ Failed to send email: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
