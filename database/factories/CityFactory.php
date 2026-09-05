<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'state' => fake()->state(),
            'country' => 'México',
            'is_active' => true,
        ];
    }
}
