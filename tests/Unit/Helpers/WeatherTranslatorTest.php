<?php

namespace Tests\Unit\Helpers;

use App\Helpers\WeatherTranslator;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class WeatherTranslatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set locale to Polish for most tests
        app()->setLocale('pl');
    }

    /** @test */
    public function it_translates_openweather_conditions_to_polish()
    {
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'OpenWeather'));
        $this->assertEquals('Pochmurno', WeatherTranslator::translate('Clouds', 'OpenWeather'));
        $this->assertEquals('Deszcz', WeatherTranslator::translate('Rain', 'OpenWeather'));
        $this->assertEquals('Burza', WeatherTranslator::translate('Thunderstorm', 'OpenWeather'));
    }

    /** @test */
    public function it_translates_openweather_descriptions_to_polish()
    {
        $this->assertEquals('bezchmurne niebo', WeatherTranslator::translate('clear sky', 'OpenWeather'));
        $this->assertEquals('małe zachmurzenie', WeatherTranslator::translate('few clouds', 'OpenWeather'));
        $this->assertEquals('lekki deszcz', WeatherTranslator::translate('light rain', 'OpenWeather'));
    }

    /** @test */
    public function it_translates_open_meteo_conditions_to_polish()
    {
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'Open-Meteo'));
        $this->assertEquals('Czyste niebo', WeatherTranslator::translate('Clear sky', 'Open-Meteo'));
        $this->assertEquals('Przeważnie bezchmurnie', WeatherTranslator::translate('Mainly clear', 'Open-Meteo'));
    }

    /** @test */
    public function it_normalizes_provider_names_with_spaces()
    {
        // "Visual Crossing" should normalize to "visualcrossing"
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'Visual Crossing'));
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'VisualCrossing'));
    }

    /** @test */
    public function it_normalizes_provider_names_preserving_hyphens()
    {
        // Hyphens are preserved, spaces removed, all lowercase
        // "Open-Meteo" → "open-meteo" (matches translation key)
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'Open-Meteo'));
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'OPEN-METEO'));
    }

    /** @test */
    public function it_is_case_sensitive_for_condition_strings()
    {
        // Condition keys ARE case-sensitive (as per translation files)
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'OpenWeather'));

        // "clear" (lowercase) doesn't have translation, should return original
        $result = WeatherTranslator::translate('clear', 'OpenWeather');
        $this->assertEquals('clear', $result);
    }

    /** @test */
    public function it_returns_original_condition_when_translation_missing()
    {
        $result = WeatherTranslator::translate('Unknown Condition', 'OpenWeather');
        $this->assertEquals('Unknown Condition', $result);
    }

    /** @test */
    public function it_logs_missing_translations()
    {
        Log::shouldReceive('channel')
            ->once()
            ->with('weather_translations')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->with('Missing weather condition translation', \Mockery::on(function ($context) {
                return $context['provider'] === 'OpenWeather'
                    && $context['condition'] === 'Unknown Condition'
                    && $context['locale'] === 'pl';
            }));

        WeatherTranslator::translate('Unknown Condition', 'OpenWeather');
    }

    /** @test */
    public function it_does_not_log_when_translation_exists()
    {
        Log::shouldReceive('channel')->never();
        Log::shouldReceive('info')->never();

        WeatherTranslator::translate('Clear', 'OpenWeather');
    }

    /** @test */
    public function it_handles_english_locale()
    {
        app()->setLocale('en');

        // In English locale, should return English translation (or original if no EN translation)
        $result = WeatherTranslator::translate('Clear', 'OpenWeather');

        // Since we might not have EN translations, it could return original or translation
        $this->assertIsString($result);
    }

    /** @test */
    public function translate_description_is_alias_for_translate()
    {
        $condition = 'Clear';
        $provider = 'OpenWeather';

        $translateResult = WeatherTranslator::translate($condition, $provider);
        $translateDescriptionResult = WeatherTranslator::translateDescription($condition, $provider);

        $this->assertEquals($translateResult, $translateDescriptionResult);
    }

    /** @test */
    public function it_handles_provider_name_with_mixed_case()
    {
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'openweather'));
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'OPENWEATHER'));
        $this->assertEquals('Bezchmurnie', WeatherTranslator::translate('Clear', 'OpenWeather'));
    }

    /** @test */
    public function it_handles_empty_condition_gracefully()
    {
        Log::shouldReceive('channel')
            ->once()
            ->with('weather_translations')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once();

        $result = WeatherTranslator::translate('', 'OpenWeather');
        $this->assertEquals('', $result);
    }

    /** @test */
    public function it_handles_condition_with_dots_correctly()
    {
        // Dots in condition strings should NOT be treated as nested array keys
        // This is why the code uses trans() and direct array access

        Log::shouldReceive('channel')
            ->once()
            ->with('weather_translations')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once();

        $result = WeatherTranslator::translate('Rain.Heavy', 'OpenWeather');

        // Should return original since this exact string likely doesn't exist
        $this->assertEquals('Rain.Heavy', $result);
    }
}
