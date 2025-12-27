<?php

namespace App\Console\Commands;

use App\Models\MonitoringRequest;
use App\Models\NotificationPreference;
use App\Mail\DailySummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestDailySummaries extends Command
{
    protected $signature = 'test:daily-summaries {email? : Email address to test with}';
    protected $description = 'Test daily summary email sending (for debugging)';

    public function handle()
    {
        $email = $this->argument('email');

        if (!$email) {
            // Show all users with notification preferences
            $preferences = NotificationPreference::with('user')->get();

            if ($preferences->isEmpty()) {
                $this->error('No notification preferences found in database!');
                $this->info('Run this to create preferences for existing users:');
                $this->line('  php artisan tinker');
                $this->line('  App\Models\User::all()->each(fn($u) => App\Models\NotificationPreference::getForUser($u->id));');
                return 1;
            }

            $this->info('Available notification preferences:');
            $this->table(
                ['ID', 'Email', 'User', 'Daily', 'Final', 'First'],
                $preferences->map(fn($p) => [
                    $p->id,
                    $p->email ?? $p->user?->email ?? 'N/A',
                    $p->user_id ? "User #{$p->user_id}" : 'Guest',
                    $p->daily_summary_enabled ? '✓' : '✗',
                    $p->final_summary_enabled ? '✓' : '✗',
                    $p->first_snapshot_enabled ? '✓' : '✗',
                ])
            );

            $email = $this->ask('Enter email to test with');
            if (!$email) {
                return 1;
            }
        }

        // Find notification preference
        $pref = NotificationPreference::where('email', $email)
            ->orWhereHas('user', fn($q) => $q->where('email', $email))
            ->first();

        if (!$pref) {
            $this->error("No notification preferences found for: {$email}");
            $this->info('Creating new preference...');
            $pref = NotificationPreference::getForEmail($email);
        }

        // Get active requests
        $requests = $pref->user_id
            ? MonitoringRequest::where('user_id', $pref->user_id)
                ->where('status', MonitoringRequest::STATUS_ACTIVE)
                ->where('notifications_enabled', true)
                ->get()
            : MonitoringRequest::where('email', $pref->email)
                ->where('status', MonitoringRequest::STATUS_ACTIVE)
                ->where('notifications_enabled', true)
                ->get();

        if ($requests->isEmpty()) {
            $this->warn("No active monitoring requests with notifications enabled for: {$email}");
            $this->info('Details:');
            $this->line("  - User ID: " . ($pref->user_id ?? 'null'));
            $this->line("  - Email: " . ($pref->email ?? $pref->user?->email));
            $this->line("  - Daily summary enabled: " . ($pref->daily_summary_enabled ? 'yes' : 'no'));
            return 1;
        }

        $this->info("Found {$requests->count()} active request(s)");

        $recipientEmail = $pref->user_id ? $pref->user->email : $pref->email;

        if (!$pref->daily_summary_enabled) {
            $this->warn('Daily summary is DISABLED for this user!');
            if (!$this->confirm('Send anyway for testing?')) {
                return 1;
            }
        }

        $this->info("Sending daily summary to: {$recipientEmail}");

        try {
            Mail::to($recipientEmail)->send(
                new DailySummary($requests, $recipientEmail, $pref->token)
            );
            $this->info("✓ Email sent successfully!");
            $this->info("Check your inbox at: {$recipientEmail}");
        } catch (\Exception $e) {
            $this->error("✗ Failed to send email: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
