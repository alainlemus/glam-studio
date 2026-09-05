<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LoyaltyCardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'code' => 'LOY-'.strtoupper(Str::random(8)),
            'stamps_required' => 10,
            'stamps_current' => 0,
            'total_rewards_claimed' => 0,
            'is_active' => true,
        ];
    }
}
