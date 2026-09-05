<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_category_id' => ProductCategory::factory(),
            'name' => fake()->unique()->words(3, true),
            'sku' => 'SKU-'.strtoupper(fake()->unique()->bothify('########')),
            'description' => fake()->sentence(),
            'cost' => fake()->randomFloat(2, 10, 200),
            'price' => fake()->randomFloat(2, 50, 500),
            'commission_percentage' => 10,
            'min_stock' => 5,
            'is_active' => true,
        ];
    }
}
