<?php

namespace App\Console\Commands;

use App\Models\PublicMonitorLocation;
use App\Models\MonitoringRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MaintainPublicMonitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitors:maintain-public';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maintain public monitoring requests for demo purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting public monitors maintenance...');

        $locations = PublicMonitorLocation::where('is_active', true)->get();

        foreach ($locations as $location) {
            $this->processLocation($location);
        }

        $this->info('Public monitors maintenance completed.');
    }

    /**
     * Process a single location
     */
    protected function processLocation(PublicMonitorLocation $location)
    {
        $this->line("Processing: {$location->name}");

        // Get active monitors for this location
        $activeMonitors = MonitoringRequest::where('location', $location->name)
            ->where('is_public', true)
            ->whereIn('status', [MonitoringRequest::STATUS_ACTIVE, MonitoringRequest::STATUS_PENDING_VERIFICATION])
            ->orderBy('created_at', 'asc')
            ->get();

        $activeCount = $activeMonitors->count();
        $this->line("  Active monitors: {$activeCount}/{$location->max_concurrent_monitors}");

        // Remove expired monitors
        $this->cleanupExpired($location);

        // Check if we need to create a new monitor
        if ($activeCount >= $location->max_concurrent_monitors) {
            $this->line("  Max concurrent monitors reached. Skipping.");
            return;
        }

        // Check if the most recent monitor is old enough to create a new one
        $latestMonitor = $activeMonitors->sortByDesc('created_at')->first();

        if ($latestMonitor) {
            $daysSinceLastCreation = now()->diffInDays($latestMonitor->created_at);

            if ($daysSinceLastCreation < $location->stagger_days) {
                $this->line("  Latest monitor created {$daysSinceLastCreation} days ago. Waiting for {$location->stagger_days} days.");
                return;
            }
        }

        // Create new monitor
        $this->createPublicMonitor($location);
    }

    /**
     * Create a new public monitor for the location
     */
    protected function createPublicMonitor(PublicMonitorLocation $location)
    {
        $targetDate = now()->addDays($location->days_ahead);
        $expiresAt = $targetDate->copy()->addDay();

        $monitor = MonitoringRequest::create([
            'user_id' => null,
            'location' => $location->name,
            'target_date' => $targetDate,
            'email' => null,
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'verification_token' => null,
            'dashboard_token' => null,
            'expires_at' => $expiresAt,
            'notifications_enabled' => false,
            'is_public' => true,
        ]);

        $this->info("  ✓ Created public monitor ID {$monitor->id} for {$location->name} (target: {$targetDate->format('Y-m-d')})");
        Log::info("Created public monitor for {$location->name}", [
            'monitor_id' => $monitor->id,
            'target_date' => $targetDate->format('Y-m-d')
        ]);
    }

    /**
     * Clean up expired monitors
     */
    protected function cleanupExpired(PublicMonitorLocation $location)
    {
        $expiredCount = MonitoringRequest::where('location', $location->name)
            ->where('is_public', true)
            ->where('status', MonitoringRequest::STATUS_ACTIVE)
            ->where('expires_at', '<', now())
            ->update(['status' => MonitoringRequest::STATUS_COMPLETED]);

        if ($expiredCount > 0) {
            $this->line("  Marked {$expiredCount} expired monitors as completed.");
        }
    }
}
