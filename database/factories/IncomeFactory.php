<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'sale_id' => null,
            'concept' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'income_date' => now()->format('Y-m-d'),
            'source' => 'service',
        ];
    }
}
