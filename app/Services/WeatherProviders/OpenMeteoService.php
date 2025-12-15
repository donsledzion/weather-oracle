<?php

namespace App\Services\WeatherProviders;

use App\Contracts\WeatherProviderInterface;
use Illuminate\Support\Facades\Http;

class OpenMeteoService implements WeatherProviderInterface
{
    protected string $baseUrl = 'https://api.open-meteo.com/v1';

    public function getName(): string
    {
        return 'Open-Meteo';
    }

    public function getForecast(string $location, string $targetDate): array
    {
        // First, geocode the location to get coordinates
        $coordinates = $this->geocodeLocation($location);

        // Get weather forecast for coordinates
        $response = Http::get("{$this->baseUrl}/forecast", [
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'daily' => 'temperature_2m_max,temperature_2m_min,temperature_2m_mean,apparent_temperature_max,weathercode,precipitation_probability_max,relative_humidity_2m_mean,surface_pressure_mean,windspeed_10m_max,winddirection_10m_dominant,cloudcover_mean',
            'timezone' => 'auto',
            'forecast_days' => 16,
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch weather data from Open-Meteo: " . $response->body());
        }

        $data = $response->json();

        return $this->formatForecastData($data, $targetDate);
    }

    protected function geocodeLocation(string $location): array
    {
        // Use Open-Meteo's geocoding API
        $response = Http::get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $location,
            'count' => 1,
            'language' => 'en',
            'format' => 'json',
        ]);

        if ($response->failed() || empty($response->json('results'))) {
            throw new \Exception("Location not found: {$location}");
        }

        $result = $response->json('results')[0];

        return [
            'latitude' => $result['latitude'],
            'longitude' => $result['longitude'],
        ];
    }

    protected function formatForecastData(array $data, string $targetDate): array
    {
        // Find the index for target date
        $targetDateOnly = date('Y-m-d', strtotime($targetDate));
        $dateIndex = array_search($targetDateOnly, $data['daily']['time']);

        if ($dateIndex === false) {
            throw new \Exception("No forecast data found for target date");
        }

        $daily = $data['daily'];

        // Map weathercode to conditions
        $weatherCode = $daily['weathercode'][$dateIndex];
        $conditions = $this->mapWeatherCode($weatherCode);

        return [
            // Forecast metadata
            'forecast_date' => $targetDateOnly . ' 12:00:00',
            'forecast_timestamp' => strtotime($targetDateOnly . ' 12:00:00'),

            // Temperature
            'temperature_min' => $daily['temperature_2m_min'][$dateIndex],
            'temperature_max' => $daily['temperature_2m_max'][$dateIndex],
            'temperature_avg' => $daily['temperature_2m_mean'][$dateIndex],
            'feels_like' => $daily['apparent_temperature_max'][$dateIndex],

            // Conditions
            'conditions' => $conditions['main'],
            'description' => $conditions['description'],

            // Precipitation
            'precipitation' => ($daily['precipitation_probability_max'][$dateIndex] ?? 0) / 100,

            // Extended data
            'humidity' => (int) round($daily['relative_humidity_2m_mean'][$dateIndex] ?? 0),
            'pressure' => (int) round($daily['surface_pressure_mean'][$dateIndex] ?? 0),
            'wind_speed' => $daily['windspeed_10m_max'][$dateIndex],
            'wind_deg' => (int) ($daily['winddirection_10m_dominant'][$dateIndex] ?? null),
            'clouds' => (int) round($daily['cloudcover_mean'][$dateIndex] ?? 0),
            'visibility' => null, // Not provided by Open-Meteo daily data

            'raw_data' => $daily,
        ];
    }

    /**
     * Map Open-Meteo WMO weather codes to human-readable conditions
     * https://open-meteo.com/en/docs
     */
    protected function mapWeatherCode(int $code): array
    {
        $codeMap = [
            0 => ['main' => 'Clear', 'description' => 'Clear sky'],
            1 => ['main' => 'Clouds', 'description' => 'Mainly clear'],
            2 => ['main' => 'Clouds', 'description' => 'Partly cloudy'],
            3 => ['main' => 'Clouds', 'description' => 'Overcast'],
            45 => ['main' => 'Fog', 'description' => 'Fog'],
            48 => ['main' => 'Fog', 'description' => 'Depositing rime fog'],
            51 => ['main' => 'Drizzle', 'description' => 'Light drizzle'],
            53 => ['main' => 'Drizzle', 'description' => 'Moderate drizzle'],
            55 => ['main' => 'Drizzle', 'description' => 'Dense drizzle'],
            61 => ['main' => 'Rain', 'description' => 'Slight rain'],
            63 => ['main' => 'Rain', 'description' => 'Moderate rain'],
            65 => ['main' => 'Rain', 'description' => 'Heavy rain'],
            71 => ['main' => 'Snow', 'description' => 'Slight snow fall'],
            73 => ['main' => 'Snow', 'description' => 'Moderate snow fall'],
            75 => ['main' => 'Snow', 'description' => 'Heavy snow fall'],
            77 => ['main' => 'Snow', 'description' => 'Snow grains'],
            80 => ['main' => 'Rain', 'description' => 'Slight rain showers'],
            81 => ['main' => 'Rain', 'description' => 'Moderate rain showers'],
            82 => ['main' => 'Rain', 'description' => 'Violent rain showers'],
            85 => ['main' => 'Snow', 'description' => 'Slight snow showers'],
            86 => ['main' => 'Snow', 'description' => 'Heavy snow showers'],
            95 => ['main' => 'Thunderstorm', 'description' => 'Thunderstorm'],
            96 => ['main' => 'Thunderstorm', 'description' => 'Thunderstorm with slight hail'],
            99 => ['main' => 'Thunderstorm', 'description' => 'Thunderstorm with heavy hail'],
        ];

        return $codeMap[$code] ?? ['main' => 'Unknown', 'description' => 'Unknown weather'];
    }
}
