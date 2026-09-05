<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'name' => 'Sucursal '.fake()->unique()->streetName(),
            'address' => fake()->address(),
            'phone' => fake()->numerify('##########'),
            'whatsapp' => fake()->numerify('##########'),
            'email' => fake()->unique()->companyEmail(),
            'manager_name' => fake()->name(),
            'opening_time' => '09:00:00',
            'closing_time' => '20:00:00',
            'opening_days' => [1, 2, 3, 4, 5, 6],
            'is_active' => true,
        ];
    }
}
