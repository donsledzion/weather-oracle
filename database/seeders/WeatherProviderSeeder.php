<?php

namespace Database\Seeders;

use App\Models\WeatherProvider;
use Illuminate\Database\Seeder;

class WeatherProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WeatherProvider::updateOrCreate(
            ['name' => 'OpenWeather'],
            [
                'configuration' => [
                    'api_key' => env('OPENWEATHER_API_KEY'),
                    'base_url' => 'https://api.openweathermap.org/data/2.5',
                ],
                'is_active' => true,
            ]
        );
    }
}
