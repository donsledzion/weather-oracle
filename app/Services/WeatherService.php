<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.api_key');
        $this->baseUrl = config('services.openweather.base_url');
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
            'temperature_min' => $closestForecast['main']['temp_min'],
            'temperature_max' => $closestForecast['main']['temp_max'],
            'temperature_avg' => $closestForecast['main']['temp'],
            'conditions' => $closestForecast['weather'][0]['main'],
            'description' => $closestForecast['weather'][0]['description'],
            'precipitation' => $closestForecast['pop'] ?? 0,
            'raw_data' => $closestForecast,
        ];
    }
}
