<?php

namespace Tests\Feature\Commands;

use App\Mail\FinalSummary;
use App\Models\MonitoringRequest;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendFinalSummariesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function it_sends_final_summaries_for_newly_completed_requests()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'final_summary_enabled' => true,
        ]);

        $request = MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => now()->subDay(),
            'status' => MonitoringRequest::STATUS_COMPLETED,
            'notifications_enabled' => true,
            'updated_at' => today(),
        ]);

        $this->artisan('notifications:send-final-summaries')->assertExitCode(0);

        Mail::assertSent(FinalSummary::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /** @test */
    public function it_skips_requests_completed_on_previous_days()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'final_summary_enabled' => true,
        ]);

        // Travel back 2 days, create completed request, travel back
        $this->travel(-2)->days();

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => now()->subDays(5),
            'status' => MonitoringRequest::STATUS_COMPLETED,
            'notifications_enabled' => true,
        ]);

        $this->travelBack();

        $this->artisan('notifications:send-final-summaries')->assertExitCode(0);

        Mail::assertNotSent(FinalSummary::class);
    }

    /** @test */
    public function it_skips_when_final_summary_disabled_in_preferences()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'final_summary_enabled' => false,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => now()->subDay(),
            'status' => MonitoringRequest::STATUS_COMPLETED,
            'notifications_enabled' => true,
            'updated_at' => today(),
        ]);

        $this->artisan('notifications:send-final-summaries')->assertExitCode(0);

        Mail::assertNotSent(FinalSummary::class);
    }

    /** @test */
    public function it_only_sends_for_requests_with_notifications_enabled()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'final_summary_enabled' => true,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => now()->subDay(),
            'status' => MonitoringRequest::STATUS_COMPLETED,
            'notifications_enabled' => false,
            'updated_at' => today(),
        ]);

        $this->artisan('notifications:send-final-summaries')->assertExitCode(0);

        Mail::assertNotSent(FinalSummary::class);
    }

    /** @test */
    public function it_sends_to_guest_users_by_email()
    {
        NotificationPreference::create([
            'email' => 'guest@example.com',
            'token' => 'test-token',
            'final_summary_enabled' => true,
        ]);

        MonitoringRequest::create([
            'email' => 'guest@example.com',
            'location' => 'Warsaw',
            'target_date' => now()->subDay(),
            'status' => MonitoringRequest::STATUS_COMPLETED,
            'notifications_enabled' => true,
            'updated_at' => today(),
        ]);

        $this->artisan('notifications:send-final-summaries')->assertExitCode(0);

        Mail::assertSent(FinalSummary::class, function ($mail) {
            return $mail->hasTo('guest@example.com');
        });
    }

    /** @test */
    public function it_handles_multiple_completed_requests()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        NotificationPreference::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'final_summary_enabled' => true,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => now()->subDay(),
            'status' => MonitoringRequest::STATUS_COMPLETED,
            'notifications_enabled' => true,
            'updated_at' => today(),
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Krakow',
            'target_date' => now()->subDay(),
            'status' => MonitoringRequest::STATUS_COMPLETED,
            'notifications_enabled' => true,
            'updated_at' => today(),
        ]);

        $this->artisan('notifications:send-final-summaries')->assertExitCode(0);

        Mail::assertSent(FinalSummary::class, 2);
    }

    /** @test */
    public function it_completes_successfully_when_no_completed_requests()
    {
        $this->artisan('notifications:send-final-summaries')->assertExitCode(0);

        Mail::assertNotSent(FinalSummary::class);
    }
}
