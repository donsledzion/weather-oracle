<?php

namespace App\Livewire;

use App\Models\ForecastSnapshot;
use App\Models\MonitoringRequest;
use App\Models\WeatherProvider;
use App\Services\WeatherProviderFactory;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MonitoringForm extends Component
{
    #[Validate('required|string|min:2')]
    public $location = '';

    #[Validate('required|date|after:today')]
    public $targetDate = '';

    #[Validate('nullable|email')]
    public $email = '';

    public function submit()
    {
        $this->validate();

        $monitoringRequest = MonitoringRequest::create([
            'location' => $this->location,
            'target_date' => $this->targetDate,
            'email' => $this->email,
            'status' => 'active',
        ]);

        // Fetch initial forecasts from all active providers
        $activeProviders = WeatherProvider::where('is_active', true)->get();
        $snapshotsCreated = 0;

        foreach ($activeProviders as $provider) {
            try {
                $weatherService = WeatherProviderFactory::make($provider);
                $forecastData = $weatherService->getForecast($this->location, $this->targetDate);

                // Check if forecast date matches target date (±1 day tolerance)
                $forecastDate = new \DateTime($forecastData['forecast_date']);
                $targetDateObj = new \DateTime($this->targetDate);
                $daysDiff = abs($forecastDate->diff($targetDateObj)->days);

                // Only save snapshot if forecast is for the target date
                if ($daysDiff <= 1) {
                    ForecastSnapshot::create([
                        'monitoring_request_id' => $monitoringRequest->id,
                        'weather_provider_id' => $provider->id,
                        'forecast_data' => $forecastData,
                        'fetched_at' => now(),
                    ]);
                    $snapshotsCreated++;
                }

            } catch (\Exception $e) {
                // Log error but continue with other providers
                \Log::warning("Failed to fetch initial forecast from {$provider->name}: " . $e->getMessage());
            }
        }

        // Clear form and validation errors
        $this->reset(['location', 'targetDate', 'email']);
        $this->resetValidation();

        // Dispatch event to refresh the list
        $this->dispatch('request-created');

        // Show appropriate message
        if ($snapshotsCreated > 0) {
            session()->flash('message', __('app.request_created_success'));
        } else {
            session()->flash('message', __('app.request_created_no_data'));
        }
    }

    public function render()
    {
        return view('livewire.monitoring-form');
    }
}
