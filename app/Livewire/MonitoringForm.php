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

        $monitoringRequest = MonitoringRequest::create([
            'location' => $this->location,
            'target_date' => $this->targetDate,
            'email' => $this->email,
            'status' => 'active',
        ]);

        // Fetch initial forecast
        try {
            $weatherService = new WeatherService();
            $forecastData = $weatherService->getForecast($this->location, $this->targetDate);

            $provider = WeatherProvider::where('name', 'OpenWeather')->first();

            if ($provider) {
                ForecastSnapshot::create([
                    'monitoring_request_id' => $monitoringRequest->id,
                    'weather_provider_id' => $provider->id,
                    'forecast_data' => $forecastData,
                    'fetched_at' => now(),
                ]);
            }

            session()->flash('message', 'Monitoring request created and initial forecast fetched successfully!');
        } catch (\Exception $e) {
            session()->flash('message', 'Monitoring request created, but failed to fetch forecast: ' . $e->getMessage());
        }

        $this->reset();
    }

    public function render()
    {
        return view('livewire.monitoring-form');
    }
}
