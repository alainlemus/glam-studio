<?php

namespace Database\Factories;

use App\Models\LoyaltyCard;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyStampFactory extends Factory
{
    public function definition(): array
    {
        return [
            'loyalty_card_id' => LoyaltyCard::factory(),
            'appointment_id' => null,
            'type' => 'earned',
            'quantity' => 1,
        ];
    }
}
