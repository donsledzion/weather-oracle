<?php

namespace Tests\Unit\Models;

use App\Models\ForecastSnapshot;
use App\Models\MonitoringRequest;
use App\Models\User;
use App\Models\WeatherProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals('pending_verification', MonitoringRequest::STATUS_PENDING_VERIFICATION);
        $this->assertEquals('active', MonitoringRequest::STATUS_ACTIVE);
        $this->assertEquals('completed', MonitoringRequest::STATUS_COMPLETED);
        $this->assertEquals('expired', MonitoringRequest::STATUS_EXPIRED);
        $this->assertEquals('rejected', MonitoringRequest::STATUS_REJECTED);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'user_id',
            'location',
            'target_date',
            'email',
            'status',
            'verification_token',
            'dashboard_token',
            'expires_at',
            'notifications_enabled',
            'is_public',
        ];

        $request = new MonitoringRequest();

        $this->assertEquals($fillable, $request->getFillable());
    }

    /** @test */
    public function it_casts_date_and_boolean_fields()
    {
        $request = MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => '2025-12-31',
            'email' => 'test@example.com',
            'status' => MonitoringRequest::STATUS_ACTIVE,
            'expires_at' => '2025-12-31 23:59:59',
            'notifications_enabled' => 1,
            'is_public' => 0,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $request->target_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $request->expires_at);
        $this->assertIsBool($request->notifications_enabled);
        $this->assertIsBool($request->is_public);

        $this->assertTrue($request->notifications_enabled);
        $this->assertFalse($request->is_public);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();

        $request = MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => '2025-12-31',
            'email' => 'test@example.com',
            'status' => MonitoringRequest::STATUS_ACTIVE,
        ]);

        $this->assertInstanceOf(User::class, $request->user);
        $this->assertEquals($user->id, $request->user->id);
    }

    /** @test */
    public function it_has_many_forecast_snapshots()
    {
        $provider = WeatherProvider::create(['name' => 'OpenWeather']);

        $request = MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => '2025-12-31',
            'email' => 'test@example.com',
            'status' => MonitoringRequest::STATUS_ACTIVE,
        ]);

        $snapshot1 = ForecastSnapshot::create([
            'monitoring_request_id' => $request->id,
            'weather_provider_id' => $provider->id,
            'forecast_data' => json_encode(['temperature' => 20, 'conditions' => 'Clear']),
            'fetched_at' => now(),
        ]);

        $snapshot2 = ForecastSnapshot::create([
            'monitoring_request_id' => $request->id,
            'weather_provider_id' => $provider->id,
            'forecast_data' => json_encode(['temperature' => 22, 'conditions' => 'Cloudy']),
            'fetched_at' => now(),
        ]);

        $this->assertCount(2, $request->forecastSnapshots);
        $this->assertTrue($request->forecastSnapshots->contains($snapshot1));
        $this->assertTrue($request->forecastSnapshots->contains($snapshot2));
    }

    /** @test */
    public function is_active_returns_true_when_status_is_active()
    {
        $request = MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => '2025-12-31',
            'email' => 'test@example.com',
            'status' => MonitoringRequest::STATUS_ACTIVE,
        ]);

        $this->assertTrue($request->isActive());
    }

    /** @test */
    public function is_active_returns_false_when_status_is_not_active()
    {
        $statuses = [
            MonitoringRequest::STATUS_PENDING_VERIFICATION,
            MonitoringRequest::STATUS_COMPLETED,
            MonitoringRequest::STATUS_EXPIRED,
            MonitoringRequest::STATUS_REJECTED,
        ];

        foreach ($statuses as $status) {
            $request = MonitoringRequest::create([
                'location' => 'Warsaw',
                'target_date' => '2025-12-31',
                'email' => "test-{$status}@example.com",
                'status' => $status,
            ]);

            $this->assertFalse($request->isActive(), "Failed for status: {$status}");
        }
    }

    /** @test */
    public function is_pending_returns_true_when_status_is_pending_verification()
    {
        $request = MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => '2025-12-31',
            'email' => 'test@example.com',
            'status' => MonitoringRequest::STATUS_PENDING_VERIFICATION,
        ]);

        $this->assertTrue($request->isPending());
    }

    /** @test */
    public function is_pending_returns_false_when_status_is_not_pending()
    {
        $statuses = [
            MonitoringRequest::STATUS_ACTIVE,
            MonitoringRequest::STATUS_COMPLETED,
            MonitoringRequest::STATUS_EXPIRED,
            MonitoringRequest::STATUS_REJECTED,
        ];

        foreach ($statuses as $status) {
            $request = MonitoringRequest::create([
                'location' => 'Warsaw',
                'target_date' => '2025-12-31',
                'email' => "test-{$status}@example.com",
                'status' => $status,
            ]);

            $this->assertFalse($request->isPending(), "Failed for status: {$status}");
        }
    }

    /** @test */
    public function is_completed_returns_true_when_status_is_completed()
    {
        $request = MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => '2025-12-31',
            'email' => 'test@example.com',
            'status' => MonitoringRequest::STATUS_COMPLETED,
        ]);

        $this->assertTrue($request->isCompleted());
    }

    /** @test */
    public function is_completed_returns_false_when_status_is_not_completed()
    {
        $statuses = [
            MonitoringRequest::STATUS_ACTIVE,
            MonitoringRequest::STATUS_PENDING_VERIFICATION,
            MonitoringRequest::STATUS_EXPIRED,
            MonitoringRequest::STATUS_REJECTED,
        ];

        foreach ($statuses as $status) {
            $request = MonitoringRequest::create([
                'location' => 'Warsaw',
                'target_date' => '2025-12-31',
                'email' => "test-{$status}@example.com",
                'status' => $status,
            ]);

            $this->assertFalse($request->isCompleted(), "Failed for status: {$status}");
        }
    }

    /** @test */
    public function active_and_pending_count_for_email_returns_correct_count()
    {
        $email = 'user@example.com';

        // Create various requests
        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => '2025-12-31',
            'email' => $email,
            'status' => MonitoringRequest::STATUS_ACTIVE,
        ]);

        MonitoringRequest::create([
            'location' => 'Krakow',
            'target_date' => '2025-12-31',
            'email' => $email,
            'status' => MonitoringRequest::STATUS_PENDING_VERIFICATION,
        ]);

        MonitoringRequest::create([
            'location' => 'Gdansk',
            'target_date' => '2025-12-31',
            'email' => $email,
            'status' => MonitoringRequest::STATUS_COMPLETED,
        ]);

        MonitoringRequest::create([
            'location' => 'Poznan',
            'target_date' => '2025-12-31',
            'email' => $email,
            'status' => MonitoringRequest::STATUS_REJECTED,
        ]);

        // Different email
        MonitoringRequest::create([
            'location' => 'Wroclaw',
            'target_date' => '2025-12-31',
            'email' => 'other@example.com',
            'status' => MonitoringRequest::STATUS_ACTIVE,
        ]);

        $count = MonitoringRequest::activeAndPendingCountForEmail($email);

        $this->assertEquals(2, $count); // Only active + pending
    }

    /** @test */
    public function active_and_pending_count_for_email_returns_zero_when_none()
    {
        $count = MonitoringRequest::activeAndPendingCountForEmail('nonexistent@example.com');

        $this->assertEquals(0, $count);
    }

    /** @test */
    public function active_count_for_user_returns_correct_count()
    {
        $user = User::factory()->create();

        // Create various requests
        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Warsaw',
            'target_date' => '2025-12-31',
            'email' => 'user@example.com',
            'status' => MonitoringRequest::STATUS_ACTIVE,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Krakow',
            'target_date' => '2025-12-31',
            'email' => 'user@example.com',
            'status' => MonitoringRequest::STATUS_ACTIVE,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Gdansk',
            'target_date' => '2025-12-31',
            'email' => 'user@example.com',
            'status' => MonitoringRequest::STATUS_PENDING_VERIFICATION,
        ]);

        MonitoringRequest::create([
            'user_id' => $user->id,
            'location' => 'Poznan',
            'target_date' => '2025-12-31',
            'email' => 'user@example.com',
            'status' => MonitoringRequest::STATUS_COMPLETED,
        ]);

        $count = MonitoringRequest::activeCountForUser($user->id);

        $this->assertEquals(2, $count); // Only active ones
    }

    /** @test */
    public function active_count_for_user_returns_zero_when_none()
    {
        $user = User::factory()->create();

        $count = MonitoringRequest::activeCountForUser($user->id);

        $this->assertEquals(0, $count);
    }
}
