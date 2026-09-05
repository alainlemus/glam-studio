<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Stylist;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalaryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'stylist_id' => Stylist::factory(),
            'branch_id' => Branch::factory(),
            'period_start' => now()->startOfMonth()->format('Y-m-d'),
            'period_end' => now()->endOfMonth()->format('Y-m-d'),
            'base_salary' => 8000,
            'commissions_total' => 0,
            'bonuses' => 0,
            'deductions' => 0,
            'total' => 8000,
            'status' => 'pending',
        ];
    }
}
