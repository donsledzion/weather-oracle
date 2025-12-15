<?php

namespace App\Contracts;

interface WeatherProviderInterface
{
    /**
     * Get weather forecast for a specific location and target date
     *
     * @param string $location City name or coordinates
     * @param string $targetDate Target date in Y-m-d format
     * @return array Normalized forecast data with keys:
     *               - forecast_date: string (Y-m-d H:i:s)
     *               - forecast_timestamp: int
     *               - temperature_min: float
     *               - temperature_max: float
     *               - temperature_avg: float
     *               - feels_like: float
     *               - conditions: string
     *               - description: string
     *               - precipitation: float (0-1)
     *               - humidity: int (%)
     *               - pressure: int (hPa)
     *               - wind_speed: float (m/s)
     *               - wind_deg: int|null (degrees)
     *               - clouds: int (%)
     *               - visibility: int|null (meters)
     *               - raw_data: array
     * @throws \Exception
     */
    public function getForecast(string $location, string $targetDate): array;

    /**
     * Get provider name
     *
     * @return string
     */
    public function getName(): string;
}
