<?php

namespace App\Livewire;

use App\Models\ForecastSnapshot;
use App\Models\MonitoringRequest;
use App\Models\WeatherProvider;
use App\Services\WeatherService;
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

        // Fetch initial forecast first to validate location
        try {
            $weatherService = new WeatherService();
            $forecastData = $weatherService->getForecast($this->location, $this->targetDate);

            $monitoringRequest = MonitoringRequest::create([
                'location' => $this->location,
                'target_date' => $this->targetDate,
                'email' => $this->email,
                'status' => 'active',
            ]);

            $provider = WeatherProvider::where('name', 'OpenWeather')->first();

            if ($provider) {
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
                    session()->flash('message', __('app.request_created_success'));
                } else {
                    session()->flash('message', __('app.request_created_no_data'));
                }
            }

            // Clear form and validation errors
            $this->reset(['location', 'targetDate', 'email']);
            $this->resetValidation();

            // Dispatch event to refresh the list
            $this->dispatch('request-created');

        } catch (\Exception $e) {
            // Handle API errors (location not found, network issues, etc.)
            $errorMessage = $e->getMessage();

            // Make error messages more user-friendly
            if (str_contains($errorMessage, '404') || str_contains($errorMessage, 'not found')) {
                session()->flash('error', __('app.location_not_found'));
            } elseif (str_contains($errorMessage, '401')) {
                session()->flash('error', __('app.api_config_error'));
            } else {
                session()->flash('error', __('app.fetch_failed', ['message' => $errorMessage]));
            }
        }
    }

    public function render()
    {
        return view('livewire.monitoring-form');
    }
}
