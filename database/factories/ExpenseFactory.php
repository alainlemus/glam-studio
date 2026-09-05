<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'expense_category_id' => ExpenseCategory::factory(),
            'branch_id' => Branch::factory(),
            'description' => fake()->sentence(4),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'expense_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'user_id' => User::factory(),
        ];
    }
}
