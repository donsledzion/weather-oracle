<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class WeatherTranslator
{
    /**
     * Translate weather condition based on provider and current locale
     * Logs missing translations to weather_translations.log
     *
     * @param string $condition Raw condition string from API
     * @param string $providerName Provider name (e.g., 'OpenWeather', 'Open-Meteo', 'Visual Crossing')
     * @return string Translated condition or original if translation missing
     */
    public static function translate(string $condition, string $providerName): string
    {
        // Normalize provider name to lowercase with hyphens for consistency
        $normalizedProvider = strtolower(str_replace(' ', '', $providerName));

        // Get translations for this provider using trans() to avoid dot notation issues
        // (dots in condition strings would be treated as nested keys by Laravel's __() helper)
        $providerTranslations = trans("weather.{$normalizedProvider}");

        // Check if translation exists (direct array access avoids dot notation problems)
        if (is_array($providerTranslations) && isset($providerTranslations[$condition])) {
            return $providerTranslations[$condition];
        }

        // Log missing translation
        Log::channel('weather_translations')->info('Missing weather condition translation', [
            'provider' => $providerName,
            'condition' => $condition,
            'locale' => app()->getLocale(),
            'timestamp' => now()->toIso8601String(),
        ]);

        // Fallback to original condition string
        return $condition;
    }

    /**
     * Translate weather description (same as translate, for semantic clarity)
     *
     * @param string $description Raw description string from API
     * @param string $providerName Provider name
     * @return string Translated description or original if translation missing
     */
    public static function translateDescription(string $description, string $providerName): string
    {
        return self::translate($description, $providerName);
    }
}
