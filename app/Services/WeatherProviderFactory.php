<?php

namespace App\Services;

use App\Contracts\WeatherProviderInterface;
use App\Models\WeatherProvider;
use App\Services\WeatherProviders\OpenMeteoService;
use App\Services\WeatherProviders\OpenWeatherService;
use App\Services\WeatherProviders\VisualCrossingService;

class WeatherProviderFactory
{
    /**
     * Create a weather provider service instance from WeatherProvider model
     *
     * @param WeatherProvider $provider
     * @return WeatherProviderInterface
     * @throws \Exception
     */
    public static function make(WeatherProvider $provider): WeatherProviderInterface
    {
        return match ($provider->name) {
            'OpenWeather' => new OpenWeatherService(),
            'Open-Meteo' => new OpenMeteoService(),
            'Visual Crossing' => new VisualCrossingService(),
            default => throw new \Exception("Unknown weather provider: {$provider->name}"),
        };
    }

    /**
     * Create a weather provider service instance by name
     *
     * @param string $providerName
     * @return WeatherProviderInterface
     * @throws \Exception
     */
    public static function makeByName(string $providerName): WeatherProviderInterface
    {
        $provider = WeatherProvider::where('name', $providerName)->first();

        if (!$provider) {
            throw new \Exception("Weather provider not found: {$providerName}");
        }

        return self::make($provider);
    }
}
