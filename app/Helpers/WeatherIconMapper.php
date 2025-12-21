<?php

namespace App\Helpers;

class WeatherIconMapper
{
    public static function getIcon(string $conditions, string $provider): string
    {
        $category = self::getCategory($conditions, $provider);

        $icons = [
            'clear' => '&#9728;',
            'partly_cloudy' => '&#127780;',
            'cloudy' => '&#9729;',
            'overcast' => '&#9729;',
            'rain' => '&#127783;',
            'drizzle' => '&#127782;',
            'heavy_rain' => '&#9928;',
            'thunderstorm' => '&#9928;',
            'snow' => '&#10052;',
            'light_snow' => '&#127784;',
            'sleet' => '&#127784;',
            'fog' => '&#127787;',
            'mist' => '&#127787;',
            'wind' => '&#128168;',
            'unknown' => '&#127758;'
        ];

        return $icons[$category] ?? $icons['unknown'];
    }

    public static function getCategory(string $conditions, string $provider): string
    {
        $conditionsLower = strtolower($conditions);

        if (preg_match('/\b(clear|sunny)\b/i', $conditionsLower)) {
            return 'clear';
        }

        if (preg_match('/\b(partly|partially|few|scattered)\s*(cloud|clouds|cloudy)\b/i', $conditionsLower)) {
            return 'partly_cloudy';
        }

        if (preg_match('/\b(overcast|heavy\s*clouds?)\b/i', $conditionsLower)) {
            return 'overcast';
        }

        if (preg_match('/\b(clouds?|cloudy)\b/i', $conditionsLower)) {
            return 'cloudy';
        }

        if (preg_match('/\b(thunder|lightning|storm)\b/i', $conditionsLower)) {
            return 'thunderstorm';
        }

        if (preg_match('/\b(heavy|torrential|intense)\s*(rain|shower)\b/i', $conditionsLower)) {
            return 'heavy_rain';
        }

        if (preg_match('/\b(drizzle|light\s*rain|sprinkle)\b/i', $conditionsLower)) {
            return 'drizzle';
        }

        if (preg_match('/\b(rain|shower|precipitation)\b/i', $conditionsLower)) {
            return 'rain';
        }

        if (preg_match('/\b(sleet|freezing\s*rain|ice\s*pellet)\b/i', $conditionsLower)) {
            return 'sleet';
        }

        if (preg_match('/\b(light\s*snow|flurr)\b/i', $conditionsLower)) {
            return 'light_snow';
        }

        if (preg_match('/\b(snow|blizzard)\b/i', $conditionsLower)) {
            return 'snow';
        }

        if (preg_match('/\b(fog|foggy)\b/i', $conditionsLower)) {
            return 'fog';
        }

        if (preg_match('/\b(mist|haze|hazy)\b/i', $conditionsLower)) {
            return 'mist';
        }

        if (preg_match('/\b(wind|windy|gust)\b/i', $conditionsLower)) {
            return 'wind';
        }

        return 'unknown';
    }

    public static function getCategoryName(string $category): string
    {
        $names = [
            'clear' => 'Clear',
            'partly_cloudy' => 'Partly Cloudy',
            'cloudy' => 'Cloudy',
            'overcast' => 'Overcast',
            'rain' => 'Rain',
            'drizzle' => 'Drizzle',
            'heavy_rain' => 'Heavy Rain',
            'thunderstorm' => 'Thunderstorm',
            'snow' => 'Snow',
            'light_snow' => 'Light Snow',
            'sleet' => 'Sleet',
            'fog' => 'Fog',
            'mist' => 'Mist',
            'wind' => 'Windy',
            'unknown' => 'Unknown'
        ];

        return $names[$category] ?? $names['unknown'];
    }
}
