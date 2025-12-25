<?php

namespace Tests\Feature\Commands;

use App\Models\MonitoringRequest;
use App\Models\WeatherProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FetchForecastsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_error_when_no_active_providers()
    {
        // No providers created

        $this->artisan('forecasts:fetch')
            ->expectsOutput('No active weather providers found')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_reports_correct_count_of_active_requests()
    {
        WeatherProvider::create(['name' => 'OpenWeather', 'is_active' => true]);

        MonitoringRequest::create([
            'location' => 'Warsaw',
            'target_date' => now()->addDays(3),
            'email' => 'active@example.com',
            'status' => MonitoringRequest::STATUS_ACTIVE,
        ]);

        MonitoringRequest::create([
            'location' => 'Krakow',
            'target_date' => now()->addDays(3),
            'email' => 'pending@example.com',
            'status' => MonitoringRequest::STATUS_PENDING_VERIFICATION,
        ]);

        MonitoringRequest::create([
            'location' => 'Gdansk',
            'target_date' => now()->addDays(3),
            'email' => 'completed@example.com',
            'status' => MonitoringRequest::STATUS_COMPLETED,
        ]);

        $this->artisan('forecasts:fetch')
            ->expectsOutput('Found 1 active monitoring requests')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_reports_active_providers_correctly()
    {
        WeatherProvider::create(['name' => 'OpenWeather', 'is_active' => true]);
        WeatherProvider::create(['name' => 'Open-Meteo', 'is_active' => true]);
        WeatherProvider::create(['name' => 'Visual Crossing', 'is_active' => false]);

        // No active requests to avoid API calls

        $this->artisan('forecasts:fetch')
            ->expectsOutput('Found 2 active providers: OpenWeather, Open-Meteo')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_completes_successfully_when_no_active_requests()
    {
        WeatherProvider::create(['name' => 'OpenWeather', 'is_active' => true]);

        // No active requests

        $this->artisan('forecasts:fetch')
            ->expectsOutput('Found 0 active monitoring requests')
            ->assertExitCode(0);
    }
}
