<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicMonitorLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists (idempotent seeder)
        if (DB::table('public_monitor_locations')->count() > 0) {
            return;
        }

        $locations = [
            // Poland
            ['name' => 'Warsaw, Poland', 'latitude' => 52.2297700, 'longitude' => 21.0117800],
            ['name' => 'Krakow, Poland', 'latitude' => 50.0646500, 'longitude' => 19.9449800],
            ['name' => 'Gdansk, Poland', 'latitude' => 54.3520300, 'longitude' => 18.6466400],

            // Europe
            ['name' => 'Berlin, Germany', 'latitude' => 52.5200070, 'longitude' => 13.4049540],
            ['name' => 'Paris, France', 'latitude' => 48.8566140, 'longitude' => 2.3522220],
            ['name' => 'London, UK', 'latitude' => 51.5073510, 'longitude' => -0.1277580],

            // USA
            ['name' => 'New York, USA', 'latitude' => 40.7127750, 'longitude' => -74.0059730],
            ['name' => 'Los Angeles, USA', 'latitude' => 34.0522340, 'longitude' => -118.2436850],

            // Asia
            ['name' => 'Tokyo, Japan', 'latitude' => 35.6761920, 'longitude' => 139.6503110],

            // Australia
            ['name' => 'Sydney, Australia', 'latitude' => -33.8688200, 'longitude' => 151.2092900],
        ];

        foreach ($locations as $location) {
            DB::table('public_monitor_locations')->insert([
                'name' => $location['name'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'is_active' => true,
                'max_concurrent_monitors' => 3,
                'days_ahead' => 10,
                'stagger_days' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
