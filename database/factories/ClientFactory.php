<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('##########'),
            'email' => fake()->safeEmail(),
            'birthday' => fake()->date(),
            'no_show_count' => 0,
            'is_blocked' => false,
            'is_active' => true,
        ];
    }
}
