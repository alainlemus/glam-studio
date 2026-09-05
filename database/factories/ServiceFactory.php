<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_category_id' => ServiceCategory::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 100, 2000),
            'commission_percentage' => 25,
            'duration_minutes' => 60,
            'is_active' => true,
        ];
    }
}
