<?php

namespace Tests\Unit\Helpers;

use App\Helpers\WeatherIconMapper;
use PHPUnit\Framework\TestCase;

class WeatherIconMapperTest extends TestCase
{
    /** @test */
    public function it_returns_clear_icon_for_sunny_conditions()
    {
        $this->assertEquals('&#9728;', WeatherIconMapper::getIcon('Clear sky', 'OpenWeather'));
        $this->assertEquals('&#9728;', WeatherIconMapper::getIcon('Sunny', 'Open-Meteo'));
    }

    /** @test */
    public function it_returns_partly_cloudy_icon_for_partial_cloud_conditions()
    {
        $this->assertEquals('&#127780;', WeatherIconMapper::getIcon('Partly cloudy', 'OpenWeather'));
        $this->assertEquals('&#127780;', WeatherIconMapper::getIcon('Partially cloudy', 'Visual Crossing'));
        $this->assertEquals('&#127780;', WeatherIconMapper::getIcon('Few clouds', 'Open-Meteo'));
        $this->assertEquals('&#127780;', WeatherIconMapper::getIcon('Scattered clouds', 'OpenWeather'));
    }

    /** @test */
    public function it_returns_cloudy_icon_for_cloudy_conditions()
    {
        $this->assertEquals('&#9729;', WeatherIconMapper::getIcon('Cloudy', 'OpenWeather'));
        $this->assertEquals('&#9729;', WeatherIconMapper::getIcon('Clouds', 'Open-Meteo'));
    }

    /** @test */
    public function it_returns_overcast_icon_for_overcast_conditions()
    {
        $this->assertEquals('&#9729;', WeatherIconMapper::getIcon('Overcast', 'OpenWeather'));
        $this->assertEquals('&#9729;', WeatherIconMapper::getIcon('Overcast clouds', 'Visual Crossing'));
        $this->assertEquals('&#9729;', WeatherIconMapper::getIcon('Heavy clouds', 'Open-Meteo'));
    }

    /** @test */
    public function it_returns_rain_icon_for_rain_conditions()
    {
        $this->assertEquals('&#127783;', WeatherIconMapper::getIcon('Rain', 'OpenWeather'));
        $this->assertEquals('&#127783;', WeatherIconMapper::getIcon('Shower', 'Open-Meteo'));
    }

    /** @test */
    public function it_returns_drizzle_icon_for_light_rain()
    {
        $this->assertEquals('&#127782;', WeatherIconMapper::getIcon('Drizzle', 'OpenWeather'));
        $this->assertEquals('&#127782;', WeatherIconMapper::getIcon('Light rain', 'Visual Crossing'));
    }

    /** @test */
    public function it_returns_heavy_rain_icon_for_heavy_rain()
    {
        $this->assertEquals('&#9928;', WeatherIconMapper::getIcon('Heavy rain', 'OpenWeather'));
        $this->assertEquals('&#9928;', WeatherIconMapper::getIcon('Intense shower', 'Visual Crossing'));
    }

    /** @test */
    public function it_returns_thunderstorm_icon_for_storm_conditions()
    {
        $this->assertEquals('&#9928;', WeatherIconMapper::getIcon('Thunderstorm', 'OpenWeather'));
        $this->assertEquals('&#9928;', WeatherIconMapper::getIcon('Thunder', 'Open-Meteo'));
        $this->assertEquals('&#9928;', WeatherIconMapper::getIcon('Lightning', 'Visual Crossing'));
    }

    /** @test */
    public function it_returns_snow_icon_for_snow_conditions()
    {
        $this->assertEquals('&#10052;', WeatherIconMapper::getIcon('Snow', 'OpenWeather'));
        $this->assertEquals('&#10052;', WeatherIconMapper::getIcon('Blizzard', 'Visual Crossing'));
    }

    /** @test */
    public function it_returns_light_snow_icon_for_light_snow()
    {
        $this->assertEquals('&#127784;', WeatherIconMapper::getIcon('Light snow', 'OpenWeather'));
        $this->assertEquals('&#127784;', WeatherIconMapper::getIcon('Flurries', 'Visual Crossing'));
    }

    /** @test */
    public function it_returns_sleet_icon_for_sleet_conditions()
    {
        $this->assertEquals('&#127784;', WeatherIconMapper::getIcon('Sleet', 'OpenWeather'));
        $this->assertEquals('&#127784;', WeatherIconMapper::getIcon('Freezing rain', 'Visual Crossing'));
        $this->assertEquals('&#127784;', WeatherIconMapper::getIcon('Ice pellets', 'Open-Meteo'));
    }

    /** @test */
    public function it_returns_fog_icon_for_fog_conditions()
    {
        $this->assertEquals('&#127787;', WeatherIconMapper::getIcon('Fog', 'OpenWeather'));
        $this->assertEquals('&#127787;', WeatherIconMapper::getIcon('Foggy', 'Visual Crossing'));
    }

    /** @test */
    public function it_returns_mist_icon_for_mist_conditions()
    {
        $this->assertEquals('&#127787;', WeatherIconMapper::getIcon('Mist', 'OpenWeather'));
        $this->assertEquals('&#127787;', WeatherIconMapper::getIcon('Haze', 'Visual Crossing'));
        $this->assertEquals('&#127787;', WeatherIconMapper::getIcon('Hazy', 'Open-Meteo'));
    }

    /** @test */
    public function it_returns_wind_icon_for_windy_conditions()
    {
        $this->assertEquals('&#128168;', WeatherIconMapper::getIcon('Wind', 'OpenWeather'));
        $this->assertEquals('&#128168;', WeatherIconMapper::getIcon('Windy', 'Visual Crossing'));
        $this->assertEquals('&#128168;', WeatherIconMapper::getIcon('Gusty', 'Open-Meteo'));
    }

    /** @test */
    public function it_returns_unknown_icon_for_unrecognized_conditions()
    {
        $this->assertEquals('&#127758;', WeatherIconMapper::getIcon('Weird weather', 'OpenWeather'));
        $this->assertEquals('&#127758;', WeatherIconMapper::getIcon('Random stuff', 'Visual Crossing'));
    }

    /** @test */
    public function it_is_case_insensitive()
    {
        $this->assertEquals('&#9728;', WeatherIconMapper::getIcon('CLEAR SKY', 'OpenWeather'));
        $this->assertEquals('&#9728;', WeatherIconMapper::getIcon('clear sky', 'OpenWeather'));
        $this->assertEquals('&#9728;', WeatherIconMapper::getIcon('Clear Sky', 'OpenWeather'));
    }

    /** @test */
    public function it_correctly_categorizes_clear_conditions()
    {
        $this->assertEquals('clear', WeatherIconMapper::getCategory('Clear sky', 'OpenWeather'));
        $this->assertEquals('clear', WeatherIconMapper::getCategory('Sunny', 'Open-Meteo'));
    }

    /** @test */
    public function it_correctly_categorizes_cloudy_conditions()
    {
        $this->assertEquals('partly_cloudy', WeatherIconMapper::getCategory('Partly cloudy', 'OpenWeather'));
        $this->assertEquals('cloudy', WeatherIconMapper::getCategory('Cloudy', 'Open-Meteo'));
        $this->assertEquals('overcast', WeatherIconMapper::getCategory('Overcast', 'Visual Crossing'));
    }

    /** @test */
    public function it_correctly_categorizes_rain_conditions()
    {
        $this->assertEquals('drizzle', WeatherIconMapper::getCategory('Drizzle', 'OpenWeather'));
        $this->assertEquals('rain', WeatherIconMapper::getCategory('Rain', 'Open-Meteo'));
        $this->assertEquals('heavy_rain', WeatherIconMapper::getCategory('Heavy rain', 'Visual Crossing'));
    }

    /** @test */
    public function it_returns_category_names()
    {
        $this->assertEquals('Clear', WeatherIconMapper::getCategoryName('clear'));
        $this->assertEquals('Partly Cloudy', WeatherIconMapper::getCategoryName('partly_cloudy'));
        $this->assertEquals('Rain', WeatherIconMapper::getCategoryName('rain'));
        $this->assertEquals('Unknown', WeatherIconMapper::getCategoryName('invalid_category'));
    }
}
