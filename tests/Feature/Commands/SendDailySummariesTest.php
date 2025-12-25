<?php

namespace Tests\Feature\Commands;

use App\Mail\DailySummary;
use App\Models\MonitoringRequest;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendDailySummariesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function it_sends_daily_summaries_to_users_with_enabled_preference()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $pref = NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'daily_summary_enabled' => true,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => now()->addDays(5),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'notifications_enabled' => true,
        ]);

        $this->artisan('notifications:send-daily-summaries')
            ->assertExitCode(0);

        Mail::assertSent(DailySummary::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /** @test */
    public function it_skips_users_without_daily_summary_enabled()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'daily_summary_enabled' => false,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => now()->addDays(5),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'notifications_enabled' => true,
        ]);

        $this->artisan('notifications:send-daily-summaries')->assertExitCode(0);

        Mail::assertNotSent(DailySummary::class);
    }

    /** @test */
    public function it_skips_users_without_active_requests()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'daily_summary_enabled' => true,
        ]);

        // No active requests

        $this->artisan('notifications:send-daily-summaries')
            ->expectsOutputToContain('1 skipped')
            ->assertExitCode(0);

        Mail::assertNotSent(DailySummary::class);
    }

    /** @test */
    public function it_only_includes_active_requests_with_notifications_enabled()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'daily_summary_enabled' => true,
        ]);

        // Active with notifications
        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => now()->addDays(5),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'notifications_enabled' => true,
        ]);

        // Active without notifications
        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Krakow',
            'target_date' => now()->addDays(5),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'notifications_enabled' => false,
        ]);

        // Completed
        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Gdansk',
            'target_date' => now()->addDays(5),
            'status' => MonitoringRequest::STATUS_COMPLETED,
            'notifications_enabled' => true,
        ]);

        $this->artisan('notifications:send-daily-summaries')->assertExitCode(0);

        Mail::assertSent(DailySummary::class);
    }

    /** @test */
    public function it_sends_to_guest_users_by_email()
    {
        $pref = NotificationPreference::create([
            'email' => 'guest@example.com',
            'token' => 'test-token',
            'daily_summary_enabled' => true,
        ]);

        MonitoringRequest::create([
            'email' => 'guest@example.com',
            'location' => 'Warsaw',
            'target_date' => now()->addDays(5),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'notifications_enabled' => true,
        ]);

        $this->artisan('notifications:send-daily-summaries')->assertExitCode(0);

        Mail::assertSent(DailySummary::class, function ($mail) {
            return $mail->hasTo('guest@example.com');
        });
    }

    /** @test */
    public function it_sends_multiple_requests_in_one_email()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'daily_summary_enabled' => true,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => now()->addDays(5),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'notifications_enabled' => true,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Krakow',
            'target_date' => now()->addDays(7),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'notifications_enabled' => true,
        ]);

        $this->artisan('notifications:send-daily-summaries')->assertExitCode(0);

        Mail::assertSent(DailySummary::class, 1);
    }

    /** @test */
    public function it_handles_multiple_users()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        NotificationPreference::create([
            'user_id' => $user1->id,
            'token' => 'token1',
            'daily_summary_enabled' => true,
        ]);

        NotificationPreference::create([
            'user_id' => $user2->id,
            'token' => 'token2',
            'daily_summary_enabled' => true,
        ]);

        MonitoringRequest::create([
            'user_id' => $user1->id,
            'location' => 'Warsaw',
            'target_date' => now()->addDays(5),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'notifications_enabled' => true,
        ]);

        MonitoringRequest::create([
            'user_id' => $user2->id,
            'location' => 'Krakow',
            'target_date' => now()->addDays(5),
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'notifications_enabled' => true,
        ]);

        $this->artisan('notifications:send-daily-summaries')->assertExitCode(0);

        Mail::assertSent(DailySummary::class, 2);
    }

    /** @test */
    public function it_completes_successfully_when_no_preferences()
    {
        $this->artisan('notifications:send-daily-summaries')
            ->expectsOutput('Sending daily summaries...')
            ->assertExitCode(0);

        Mail::assertNotSent(DailySummary::class);
    }
}
