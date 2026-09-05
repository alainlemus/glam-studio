<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStockFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'branch_id' => Branch::factory(),
            'stock' => 20,
            'min_stock' => 5,
        ];
    }
}
