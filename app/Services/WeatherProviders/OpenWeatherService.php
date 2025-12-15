<?php

namespace App\Services\WeatherProviders;

use App\Contracts\WeatherProviderInterface;
use Illuminate\Support\Facades\Http;

class OpenWeatherService implements WeatherProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.api_key');
        $this->baseUrl = config('services.openweather.base_url');
    }

    public function getName(): string
    {
        return 'OpenWeather';
    }

    public function getForecast(string $location, string $targetDate): array
    {
        // OpenWeather API endpoint for 5-day forecast
        $response = Http::get("{$this->baseUrl}/forecast", [
            'q' => $location,
            'appid' => $this->apiKey,
            'units' => 'metric',
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch weather data: " . $response->body());
        }

        $data = $response->json();

        // Extract relevant forecast data
        return $this->formatForecastData($data, $targetDate);
    }

    protected function formatForecastData(array $data, string $targetDate): array
    {
        // Find forecast closest to target date
        $targetTimestamp = strtotime($targetDate . ' 12:00:00');
        $closestForecast = null;
        $minDiff = PHP_INT_MAX;

        foreach ($data['list'] as $forecast) {
            $forecastTimestamp = $forecast['dt'];
            $diff = abs($forecastTimestamp - $targetTimestamp);

            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closestForecast = $forecast;
            }
        }

        if (!$closestForecast) {
            throw new \Exception("No forecast data found for target date");
        }

        return [
            // Forecast metadata
            'forecast_date' => $closestForecast['dt_txt'],
            'forecast_timestamp' => $closestForecast['dt'],

            // Temperature
            'temperature_min' => $closestForecast['main']['temp_min'],
            'temperature_max' => $closestForecast['main']['temp_max'],
            'temperature_avg' => $closestForecast['main']['temp'],
            'feels_like' => $closestForecast['main']['feels_like'],

            // Conditions
            'conditions' => $closestForecast['weather'][0]['main'],
            'description' => $closestForecast['weather'][0]['description'],

            // Precipitation
            'precipitation' => $closestForecast['pop'] ?? 0,

            // Extended data
            'humidity' => $closestForecast['main']['humidity'],
            'pressure' => $closestForecast['main']['pressure'],
            'wind_speed' => $closestForecast['wind']['speed'],
            'wind_deg' => $closestForecast['wind']['deg'] ?? null,
            'clouds' => $closestForecast['clouds']['all'],
            'visibility' => $closestForecast['visibility'] ?? null,

            'raw_data' => $closestForecast,
        ];
    }
}
