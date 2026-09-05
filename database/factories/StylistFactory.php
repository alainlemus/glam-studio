<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StylistFactory extends Factory
{
    public function definition(): array
    {
        $branch = Branch::factory();

        return [
            'user_id' => User::factory()->state(['role' => User::ROLE_STYLIST]),
            'branch_id' => $branch,
            'specialty' => fake()->randomElement(['Cortes', 'Coloración', 'Maquillaje', 'Uñas']),
            'bio' => fake()->sentence(),
            'base_salary' => 8000,
            'service_commission' => 25,
            'product_commission' => 10,
            'is_active' => true,
        ];
    }
}
