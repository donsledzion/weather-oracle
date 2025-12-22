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
        // Clear old locations first (to allow re-seeding with new data)
        DB::table('public_monitor_locations')->truncate();

        $locations = [
            // Polish cities - mountains
            ['name' => 'Zakopane', 'latitude' => 49.2992200, 'longitude' => 19.9495800],

            // Polish cities - southeast
            ['name' => 'Ustrzyki Dolne', 'latitude' => 49.4307900, 'longitude' => 22.5928400],

            // Polish cities - northeast
            ['name' => 'Suwałki', 'latitude' => 54.1116200, 'longitude' => 22.9305600],

            // Polish cities - coast
            ['name' => 'Łeba', 'latitude' => 54.7595200, 'longitude' => 17.5613900],
            ['name' => 'Hel', 'latitude' => 54.6083300, 'longitude' => 18.8027800],

            // Polish cities - west
            ['name' => 'Szczecin', 'latitude' => 53.4285400, 'longitude' => 14.5528100],

            // Polish cities - major cities
            ['name' => 'Toruń', 'latitude' => 53.0137900, 'longitude' => 18.5984400],
            ['name' => 'Kraków', 'latitude' => 50.0646500, 'longitude' => 19.9449800],
            ['name' => 'Wrocław', 'latitude' => 51.1078900, 'longitude' => 17.0385400],
            ['name' => 'Gdańsk', 'latitude' => 54.3520300, 'longitude' => 18.6466400],
            ['name' => 'Warszawa', 'latitude' => 52.2297700, 'longitude' => 21.0117800],
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
