<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'branch_id' => Branch::factory(),
            'stylist_id' => null,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'status' => 'pending',
            'source' => 'admin',
            'total' => 0,
            'deposit' => 0,
        ];
    }
}
