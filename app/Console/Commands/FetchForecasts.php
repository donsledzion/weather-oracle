<?php

namespace App\Console\Commands;

use App\Models\ForecastSnapshot;
use App\Models\MonitoringRequest;
use App\Models\WeatherProvider;
use App\Models\NotificationPreference;
use App\Mail\FirstSnapshotNotification;
use App\Services\WeatherProviderFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class FetchForecasts extends Command
{
    protected $signature = 'forecasts:fetch';
    protected $description = 'Fetch weather forecasts for all active monitoring requests from all providers';

    public function handle()
    {
        $this->info('Starting forecast fetch...');

        // Fetch only requests with 'active' status using constant
        $activeRequests = MonitoringRequest::where('status', MonitoringRequest::STATUS_ACTIVE)->get();
        $activeProviders = WeatherProvider::where('is_active', true)->get();

        if ($activeProviders->isEmpty()) {
            $this->error('No active weather providers found');
            return 1;
        }

        $this->info("Found {$activeRequests->count()} active monitoring requests");
        $this->info("Found {$activeProviders->count()} active providers: " . $activeProviders->pluck('name')->join(', '));

        $successCount = 0;
        $failCount = 0;

        foreach ($activeRequests as $request) {
            foreach ($activeProviders as $provider) {
                try {
                    $weatherService = WeatherProviderFactory::make($provider);

                    $forecastData = $weatherService->getForecast(
                        $request->location,
                        $request->target_date->format('Y-m-d')
                    );

                    // Check if forecast date matches target date (±1 day tolerance)
                    $forecastDate = new \DateTime($forecastData['forecast_date']);
                    $targetDate = new \DateTime($request->target_date->format('Y-m-d'));
                    $daysDiff = abs($forecastDate->diff($targetDate)->days);

                    // Only save snapshot if forecast is for the target date
                    if ($daysDiff <= 1) {
                        // Check if this is the first snapshot for this provider
                        $isFirstSnapshot = !ForecastSnapshot::where('monitoring_request_id', $request->id)
                            ->where('weather_provider_id', $provider->id)
                            ->exists();

                        $snapshot = ForecastSnapshot::create([
                            'monitoring_request_id' => $request->id,
                            'weather_provider_id' => $provider->id,
                            'forecast_data' => $forecastData,
                            'fetched_at' => now(),
                        ]);

                        $this->info("✓ [{$provider->name}] {$request->location}");
                        $successCount++;

                        // Send notification if this is first snapshot
                        if ($isFirstSnapshot) {
                            $this->sendFirstSnapshotNotification($request, $snapshot);
                        }
                    } else {
                        $this->info("⚠ [{$provider->name}] {$request->location}: target date too far (forecast for {$forecastData['forecast_date']})");
                    }

                } catch (\Exception $e) {
                    $this->error("✗ [{$provider->name}] {$request->location}: {$e->getMessage()}");
                    $failCount++;
                }
            }
        }

        $this->info("\nCompleted: {$successCount} successful, {$failCount} failed");

        return 0;
    }

    /**
     * Send first snapshot notification email
     */
    protected function sendFirstSnapshotNotification(MonitoringRequest $request, ForecastSnapshot $snapshot): void
    {
        // Check if request has notifications enabled
        if (!$request->notifications_enabled) {
            return;
        }

        // Get notification preferences
        $preferences = $request->user_id
            ? NotificationPreference::getForUser($request->user_id)
            : NotificationPreference::getForEmail($request->email);

        // Check if first snapshot notifications are enabled
        if (!$preferences->first_snapshot_enabled) {
            return;
        }

        // Get recipient email
        $recipientEmail = $request->user_id
            ? $request->user->email
            : $request->email;

        try {
            Mail::to($recipientEmail)->send(
                new FirstSnapshotNotification($request, $snapshot, $preferences->token)
            );
            $this->info("  📧 First snapshot notification sent to {$recipientEmail}");
        } catch (\Exception $e) {
            $this->error("  ✗ Failed to send notification: {$e->getMessage()}");
        }
    }
}
