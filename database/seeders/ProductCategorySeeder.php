<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Shampoos', 'icon' => '🧴', 'description' => 'Shampoos profesionales para todo tipo de cabello.'],
            ['name' => 'Acondicionadores', 'icon' => '🧴', 'description' => 'Acondicionadores y suavizantes.'],
            ['name' => 'Tratamientos', 'icon' => '✨', 'description' => 'Sueros, keratina y tratamientos especiales.'],
            ['name' => 'Fijadores', 'icon' => '💨', 'description' => 'Sprays, geles y ceras.'],
            ['name' => 'Tintes', 'icon' => '🎨', 'description' => 'Tintes profesionales.'],
            ['name' => 'Herramientas', 'icon' => '🪮', 'description' => 'Secadoras, planchas y más.'],
            ['name' => 'Cosméticos', 'icon' => '💄', 'description' => 'Maquillaje y cuidado de piel.'],
            ['name' => 'Accesorios', 'icon' => '🎀', 'description' => 'Accesorios para el cabello.'],
        ];

        foreach ($categories as $cat) {
            $cat['slug'] = \Illuminate\Support\Str::slug($cat['name']);
            ProductCategory::firstOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}