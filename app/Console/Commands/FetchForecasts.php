<?php

namespace App\Console\Commands;

use App\Models\ForecastSnapshot;
use App\Models\MonitoringRequest;
use App\Models\WeatherProvider;
use App\Services\WeatherService;
use Illuminate\Console\Command;

class FetchForecasts extends Command
{
    protected $signature = 'forecasts:fetch';
    protected $description = 'Fetch weather forecasts for all active monitoring requests';

    public function handle()
    {
        $this->info('Starting forecast fetch...');

        $activeRequests = MonitoringRequest::where('status', 'active')->get();
        $provider = WeatherProvider::where('name', 'OpenWeather')->first();

        if (!$provider) {
            $this->error('OpenWeather provider not found in database');
            return 1;
        }

        $this->info("Found {$activeRequests->count()} active monitoring requests");

        $weatherService = new WeatherService();
        $successCount = 0;
        $failCount = 0;

        foreach ($activeRequests as $request) {
            try {
                $forecastData = $weatherService->getForecast(
                    $request->location,
                    $request->target_date->format('Y-m-d')
                );

                ForecastSnapshot::create([
                    'monitoring_request_id' => $request->id,
                    'weather_provider_id' => $provider->id,
                    'forecast_data' => $forecastData,
                    'fetched_at' => now(),
                ]);

                $this->info("✓ Fetched forecast for: {$request->location}");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("✗ Failed for {$request->location}: {$e->getMessage()}");
                $failCount++;
            }
        }

        $this->info("\nCompleted: {$successCount} successful, {$failCount} failed");

        return 0;
    }
}
