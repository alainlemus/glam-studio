<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MarketingCampaignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->catchPhrase(),
            'description' => fake()->sentence(),
            'type' => 'whatsapp',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => null,
            'branch_id' => null,
            'service_id' => null,
            'discount_percentage' => 10,
            'message_template' => 'Hola {nombre}, tenemos una promoción para ti.',
            'status' => 'draft',
            'target_audience' => 0,
            'messages_sent' => 0,
        ];
    }
}
