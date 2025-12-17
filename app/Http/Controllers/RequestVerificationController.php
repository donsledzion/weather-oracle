<?php

namespace App\Http\Controllers;

use App\Models\ForecastSnapshot;
use App\Models\MonitoringRequest;
use App\Models\WeatherProvider;
use App\Services\WeatherProviderFactory;
use Illuminate\Http\Request;

class RequestVerificationController extends Controller
{
    /**
     * Verify and activate a monitoring request
     */
    public function verify(string $token)
    {
        $request = MonitoringRequest::where('verification_token', $token)
            ->where('status', MonitoringRequest::STATUS_PENDING_VERIFICATION)
            ->first();

        if (!$request) {
            return view('verification-result', [
                'success' => false,
                'message' => 'Invalid or expired verification link.',
            ]);
        }

        // Check if expired
        if ($request->expires_at && $request->expires_at->isPast()) {
            $request->update(['status' => MonitoringRequest::STATUS_EXPIRED]);

            return view('verification-result', [
                'success' => false,
                'message' => 'This verification link has expired (2 hours limit).',
            ]);
        }

        // Activate request
        $request->update(['status' => MonitoringRequest::STATUS_ACTIVE]);

        // Immediately fetch initial forecasts from all active providers
        $this->fetchInitialForecasts($request);

        // Redirect to guest dashboard
        return redirect()->route('guest.dashboard', ['token' => $request->dashboard_token])
            ->with('success', 'Your monitoring request has been activated!');
    }

    /**
     * Reject a monitoring request
     */
    public function reject(string $token)
    {
        $request = MonitoringRequest::where('verification_token', $token)
            ->whereIn('status', [MonitoringRequest::STATUS_PENDING_VERIFICATION, MonitoringRequest::STATUS_ACTIVE])
            ->first();

        if (!$request) {
            return view('verification-result', [
                'success' => false,
                'message' => 'Invalid link or request already processed.',
            ]);
        }

        // Mark as rejected
        $request->update(['status' => MonitoringRequest::STATUS_REJECTED]);

        return view('verification-result', [
            'success' => true,
            'message' => 'Your monitoring request has been cancelled.',
        ]);
    }

    /**
     * Fetch initial forecasts from all active providers for newly activated request
     */
    protected function fetchInitialForecasts(MonitoringRequest $request): void
    {
        $activeProviders = WeatherProvider::where('is_active', true)->get();

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
                    ForecastSnapshot::create([
                        'monitoring_request_id' => $request->id,
                        'weather_provider_id' => $provider->id,
                        'forecast_data' => $forecastData,
                        'fetched_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Silently fail - scheduler will retry later
                \Log::warning("Failed to fetch initial forecast from {$provider->name}: {$e->getMessage()}");
            }
        }
    }
}
