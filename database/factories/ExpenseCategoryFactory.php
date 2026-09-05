<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'type' => 'variable',
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
