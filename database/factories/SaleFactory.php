<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => null,
            'branch_id' => Branch::factory(),
            'stylist_id' => null,
            'user_id' => User::factory(),
            'appointment_id' => null,
            'status' => 'paid',
            'payment_method' => 'cash',
            'subtotal' => 300,
            'discount' => 0,
            'tax' => 0,
            'total' => 300,
        ];
    }
}
