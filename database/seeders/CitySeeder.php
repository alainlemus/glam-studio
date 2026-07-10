<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Ciudad de México', 'state' => 'CDMX'],
            ['name' => 'Guadalajara', 'state' => 'Jalisco'],
            ['name' => 'Monterrey', 'state' => 'Nuevo León'],
            ['name' => 'Puebla', 'state' => 'Puebla'],
            ['name' => 'Querétaro', 'state' => 'Querétaro'],
            ['name' => 'Cancún', 'state' => 'Quintana Roo'],
            ['name' => 'Mérida', 'state' => 'Yucatán'],
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(
                ['name' => $city['name'], 'state' => $city['state']],
                ['country' => 'México', 'is_active' => true]
            );
        }
    }
}