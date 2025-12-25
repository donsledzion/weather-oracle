<?php

namespace Tests\Feature\Commands;

use App\Models\MonitoringRequest;
use App\Models\PublicMonitorLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintainPublicMonitorsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_completes_successfully_when_no_active_locations()
    {
        // No locations created

        $this->artisan('monitors:maintain-public')
            ->expectsOutput('Starting public monitors maintenance...')
            ->expectsOutput('Public monitors maintenance completed.')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_processes_active_locations_only()
    {
        PublicMonitorLocation::create([
            'name' => 'Warsaw',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_active' => true,
            'max_concurrent_monitors' => 3,
            'stagger_days' => 1,
            'days_ahead' => 7,
        ]);

        PublicMonitorLocation::create([
            'name' => 'Krakow',
            'latitude' => 50.0647,
            'longitude' => 19.9450,
            'is_active' => false, // Inactive
            'max_concurrent_monitors' => 3,
            'stagger_days' => 1,
            'days_ahead' => 7,
        ]);

        $this->artisan('monitors:maintain-public')
            ->expectsOutput('Processing: Warsaw')
            ->doesntExpectOutput('Processing: Krakow')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_creates_monitor_when_none_exist()
    {
        $location = PublicMonitorLocation::create([
            'name' => 'Warsaw',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_active' => true,
            'max_concurrent_monitors' => 3,
            'stagger_days' => 1,
            'days_ahead' => 7,
        ]);

        $this->artisan('monitors:maintain-public')->assertExitCode(0);

        // Should create one monitor
        $this->assertDatabaseHas('monitoring_requests', [
            'location' => 'Warsaw',
            'is_public' => true,
            'status' => MonitoringRequest::STATUS_ACTIVE,
        ]);

        $monitor = MonitoringRequest::where('location', 'Warsaw')->first();
        $this->assertEquals(now()->addDays(7)->format('Y-m-d'), $monitor->target_date->format('Y-m-d'));
    }

    /** @test */
    public function it_does_not_create_monitor_when_max_concurrent_reached()
    {
        $location = PublicMonitorLocation::create([
            'name' => 'Warsaw',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_active' => true,
            'max_concurrent_monitors' => 2,
            'stagger_days' => 1,
            'days_ahead' => 7,
        ]);

        // Create 2 active monitors (max)
        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->addDays(7),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'is_public' => true,
        ]);

        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->addDays(8),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'is_public' => true,
        ]);

        $this->artisan('monitors:maintain-public')
            ->expectsOutputToContain('Max concurrent monitors reached')
            ->assertExitCode(0);

        // Should still be only 2 monitors
        $this->assertCount(2, MonitoringRequest::where('location', 'Warsaw')->get());
    }

    /** @test */
    public function it_respects_stagger_days()
    {
        $location = PublicMonitorLocation::create([
            'name' => 'Warsaw',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_active' => true,
            'max_concurrent_monitors' => 5,
            'stagger_days' => 3, // Wait 3 days
            'days_ahead' => 7,
        ]);

        // Create a monitor 1 day ago (not enough stagger)
        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->addDays(7),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'is_public' => true,
            'created_at' => now()->subDay(),
        ]);

        $this->artisan('monitors:maintain-public')
            ->expectsOutputToContain('Waiting for 3 days')
            ->assertExitCode(0);

        // Should still be only 1 monitor
        $this->assertCount(1, MonitoringRequest::where('location', 'Warsaw')->get());
    }

    /** @test */
    public function it_creates_new_monitor_after_stagger_period()
    {
        $location = PublicMonitorLocation::create([
            'name' => 'Warsaw',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_active' => true,
            'max_concurrent_monitors' => 5,
            'stagger_days' => 2,
            'days_ahead' => 7,
        ]);

        // Travel back in time, create monitor, then travel back to present
        $this->travel(-4)->days();

        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->addDays(7),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'is_public' => true,
        ]);

        $this->travelBack();

        $this->artisan('monitors:maintain-public')->assertExitCode(0);

        // Should create a new monitor
        $this->assertCount(2, MonitoringRequest::where('location', 'Warsaw')->get());
    }

    /** @test */
    public function it_marks_expired_monitors_as_completed()
    {
        $location = PublicMonitorLocation::create([
            'name' => 'Warsaw',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_active' => true,
            'max_concurrent_monitors' => 3,
            'stagger_days' => 1,
            'days_ahead' => 7,
        ]);

        // Create expired monitor
        $expiredMonitor = MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->subDays(2),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'is_public' => true,
            'expires_at' => now()->subHour(),
        ]);

        $this->artisan('monitors:maintain-public')
            ->expectsOutputToContain('Marked 1 expired monitors as completed')
            ->assertExitCode(0);

        // Monitor should be marked as completed
        $expiredMonitor->refresh();
        $this->assertEquals(MonitoringRequest::STATUS_COMPLETED, $expiredMonitor->status);
    }

    /** @test */
    public function it_counts_both_active_and_pending_as_active()
    {
        $location = PublicMonitorLocation::create([
            'name' => 'Warsaw',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_active' => true,
            'max_concurrent_monitors' => 2,
            'stagger_days' => 1,
            'days_ahead' => 7,
        ]);

        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->addDays(7),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'is_public' => true,
        ]);

        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->addDays(8),
            'status' => MonitoringRequest::STATUS_PENDING_VERIFICATION,
            'is_public' => true,
        ]);

        $this->artisan('monitors:maintain-public')
            ->expectsOutput('  Active monitors: 2/2')
            ->expectsOutputToContain('Max concurrent monitors reached')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_ignores_completed_monitors_in_count()
    {
        $location = PublicMonitorLocation::create([
            'name' => 'Warsaw',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_active' => true,
            'max_concurrent_monitors' => 3,
            'stagger_days' => 1,
            'days_ahead' => 7,
        ]);

        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->addDays(7),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'is_public' => true,
        ]);

        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->subDays(1),
            'status' => MonitoringRequest::STATUS_COMPLETED,
            'is_public' => true,
        ]);

        $this->artisan('monitors:maintain-public')
            ->expectsOutput('  Active monitors: 1/3')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_sets_correct_expiration_date()
    {
        $location = PublicMonitorLocation::create([
            'name' => 'Warsaw',
            'latitude' => 52.2297,
            'longitude' => 21.0122,
            'is_active' => true,
            'max_concurrent_monitors' => 3,
            'stagger_days' => 1,
            'days_ahead' => 5,
        ]);

        $this->artisan('monitors:maintain-public')->assertExitCode(0);

        $monitor = MonitoringRequest::where('location', 'Warsaw')->first();

        // Expires 1 day after target date
        $expectedExpiry = now()->addDays(5)->addDay();
        $this->assertEquals($expectedExpiry->format('Y-m-d'), $monitor->expires_at->format('Y-m-d'));
    }
}
