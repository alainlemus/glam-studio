<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TestimonialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_name' => fake()->name(),
            'quote' => fake()->sentence(10),
            'rating' => 5,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
