<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            'Shampoos' => [
                ['name' => 'Shampoo Hidratante 250ml', 'cost' => 80, 'price' => 220],
                ['name' => 'Shampoo Anticaspa 400ml', 'cost' => 120, 'price' => 320],
                ['name' => 'Shampoo Reparador 500ml', 'cost' => 150, 'price' => 380],
                ['name' => 'Shampoo Volumen 300ml', 'cost' => 100, 'price' => 280],
            ],
            'Acondicionadores' => [
                ['name' => 'Acondicionador Hidratante 250ml', 'cost' => 90, 'price' => 240],
                ['name' => 'Acondicionador Reparador 500ml', 'cost' => 160, 'price' => 400],
            ],
            'Tratamientos' => [
                ['name' => 'Keratina Capilar Kit', 'cost' => 800, 'price' => 1800],
                ['name' => 'Suero Reparador 100ml', 'cost' => 200, 'price' => 480],
                ['name' => 'Mascarilla Capilar 500g', 'cost' => 220, 'price' => 520],
                ['name' => 'Ampolleta Anticaída', 'cost' => 150, 'price' => 380],
            ],
            'Fijadores' => [
                ['name' => 'Spray Fijador Fuerte 400ml', 'cost' => 90, 'price' => 250],
                ['name' => 'Gel Modelador 250ml', 'cost' => 70, 'price' => 200],
                ['name' => 'Cera Modeladora 100ml', 'cost' => 110, 'price' => 290],
            ],
            'Tintes' => [
                ['name' => 'Tinte Profesional Negro', 'cost' => 180, 'price' => 450],
                ['name' => 'Tinte Castaño Claro', 'cost' => 180, 'price' => 450],
                ['name' => 'Tinte Rubio', 'cost' => 180, 'price' => 450],
                ['name' => 'Tinte Rojo Intenso', 'cost' => 200, 'price' => 480],
            ],
            'Herramientas' => [
                ['name' => 'Secadora Profesional', 'cost' => 1200, 'price' => 2800],
                ['name' => 'Plancha para Cabello', 'cost' => 800, 'price' => 1900],
                ['name' => 'Rizador Cerámico', 'cost' => 900, 'price' => 2100],
            ],
            'Cosméticos' => [
                ['name' => 'Base de Maquillaje', 'cost' => 250, 'price' => 580],
                ['name' => 'Labial Mate', 'cost' => 120, 'price' => 320],
                ['name' => 'Máscara de Pestañas', 'cost' => 180, 'price' => 420],
            ],
            'Accesorios' => [
                ['name' => 'Cepillo Profesional', 'cost' => 100, 'price' => 280],
                ['name' => 'Cepillo Desenredante', 'cost' => 80, 'price' => 220],
                ['name' => 'Pinzas Decorativas Set', 'cost' => 50, 'price' => 150],
            ],
        ];

        foreach ($products as $categoryName => $items) {
            $category = ProductCategory::where('name', $categoryName)->first();
            if (!$category) continue;

            foreach ($items as $productData) {
                $productData['slug'] = \Illuminate\Support\Str::slug($productData['name']);
                $productData['sku'] = 'SKU-' . strtoupper(\Illuminate\Support\Str::random(8));
                $product = Product::firstOrCreate(
                    ['name' => $productData['name']],
                    array_merge($productData, [
                        'product_category_id' => $category->id,
                        'commission_percentage' => 10.00,
                        'min_stock' => 5,
                        'is_active' => true,
                    ])
                );

                foreach (Branch::all() as $branch) {
                    ProductStock::firstOrCreate(
                        ['product_id' => $product->id, 'branch_id' => $branch->id],
                        [
                            'stock' => fake()->numberBetween(10, 50),
                            'min_stock' => 5,
                        ]
                    );
                }
            }
        }
    }
}