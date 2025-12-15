<?php

namespace App\Services;

/**
 * Backward compatibility wrapper for WeatherService
 * @deprecated Use WeatherProviderFactory instead
 */
class WeatherService
{
    protected $openWeatherService;

    public function __construct()
    {
        $this->openWeatherService = WeatherProviderFactory::makeByName('OpenWeather');
    }

    public function getForecast(string $location, string $targetDate): array
    {
        return $this->openWeatherService->getForecast($location, $targetDate);
    }
}
