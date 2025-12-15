<?php

namespace App\Services\WeatherProviders;

use App\Contracts\WeatherProviderInterface;
use Illuminate\Support\Facades\Http;

class VisualCrossingService implements WeatherProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.visualcrossing.api_key');
        $this->baseUrl = config('services.visualcrossing.base_url');
    }

    public function getName(): string
    {
        return 'Visual Crossing';
    }

    public function getForecast(string $location, string $targetDate): array
    {
        // Visual Crossing Timeline API
        $url = "{$this->baseUrl}/{$location}/{$targetDate}";

        $response = Http::get($url, [
            'key' => $this->apiKey,
            'unitGroup' => 'metric',
            'include' => 'days',
            'elements' => 'datetime,tempmax,tempmin,temp,feelslike,humidity,precip,precipprob,pressure,windspeed,winddir,cloudcover,visibility,conditions,description',
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch weather data from Visual Crossing: " . $response->body());
        }

        $data = $response->json();

        return $this->formatForecastData($data, $targetDate);
    }

    protected function formatForecastData(array $data, string $targetDate): array
    {
        if (empty($data['days'])) {
            throw new \Exception("No forecast data found for target date");
        }

        $dayData = $data['days'][0];

        return [
            // Forecast metadata
            'forecast_date' => $targetDate . ' 12:00:00',
            'forecast_timestamp' => strtotime($targetDate . ' 12:00:00'),

            // Temperature
            'temperature_min' => $dayData['tempmin'] ?? 0,
            'temperature_max' => $dayData['tempmax'] ?? 0,
            'temperature_avg' => $dayData['temp'] ?? 0,
            'feels_like' => $dayData['feelslike'] ?? 0,

            // Conditions
            'conditions' => $dayData['conditions'] ?? 'Unknown',
            'description' => $dayData['description'] ?? '',

            // Precipitation
            'precipitation' => ($dayData['precipprob'] ?? 0) / 100,

            // Extended data
            'humidity' => (int) ($dayData['humidity'] ?? 0),
            'pressure' => (int) ($dayData['pressure'] ?? 0),
            'wind_speed' => $dayData['windspeed'] ?? 0,
            'wind_deg' => (int) ($dayData['winddir'] ?? 0),
            'clouds' => (int) ($dayData['cloudcover'] ?? 0),
            'visibility' => isset($dayData['visibility']) ? (int) ($dayData['visibility'] * 1000) : null, // km to meters

            'raw_data' => $dayData,
        ];
    }
}
