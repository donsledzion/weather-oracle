<?php

namespace App\Console\Commands;

use App\Models\ActualWeather;
use App\Models\MonitoringRequest;
use App\Services\WeatherService;
use Illuminate\Console\Command;

class CheckTargetDates extends Command
{
    protected $signature = 'targets:check';
    protected $description = 'Check for monitoring requests that reached target date and fetch actual weather';

    public function handle()
    {
        $this->info('Checking for reached target dates...');

        // Find active requests where target date has passed
        $reachedRequests = MonitoringRequest::where('status', 'active')
            ->whereDate('target_date', '<=', now())
            ->get();

        $this->info("Found {$reachedRequests->count()} requests that reached target date");

        $weatherService = new WeatherService();
        $successCount = 0;
        $failCount = 0;

        foreach ($reachedRequests as $request) {
            try {
                // Fetch actual weather for target date
                $actualData = $weatherService->getForecast(
                    $request->location,
                    $request->target_date->format('Y-m-d')
                );

                // Store actual weather
                ActualWeather::create([
                    'monitoring_request_id' => $request->id,
                    'actual_data' => $actualData,
                    'fetched_at' => now(),
                ]);

                // Mark request as completed
                $request->update(['status' => 'completed']);

                $this->info("✓ Completed monitoring for: {$request->location}");
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
