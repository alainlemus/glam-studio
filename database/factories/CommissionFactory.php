<?php

namespace Database\Factories;

use App\Models\Stylist;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'stylist_id' => Stylist::factory(),
            'sale_id' => null,
            'appointment_id' => null,
            'type' => 'service',
            'base_amount' => 300,
            'percentage' => 25,
            'amount' => 75,
            'status' => 'pending',
        ];
    }
}
