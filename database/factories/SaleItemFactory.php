<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'itemable_id' => Service::factory(),
            'itemable_type' => Service::class,
            'name' => fake()->words(3, true),
            'quantity' => 1,
            'unit_price' => 300,
            'subtotal' => 300,
            'commission_percentage' => 25,
            'commission_amount' => 75,
            'stylist_id' => null,
        ];
    }
}
