<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Corte y Peinado', 'icon' => '✂️', 'description' => 'Cortes modernos para dama y caballero.', 'sort_order' => 1],
            ['name' => 'Coloración', 'icon' => '🎨', 'description' => 'Tintes, mechas, balayage y más.', 'sort_order' => 2],
            ['name' => 'Tratamientos Capilares', 'icon' => '💆', 'description' => 'Keratina, hidratación profunda, botox capilar.', 'sort_order' => 3],
            ['name' => 'Uñas', 'icon' => '💅', 'description' => 'Manicure, pedicure, gel, acrílico.', 'sort_order' => 4],
            ['name' => 'Maquillaje', 'icon' => '💄', 'description' => 'Maquillaje profesional para eventos.', 'sort_order' => 5],
            ['name' => 'Cejas y Pestañas', 'icon' => '👁️', 'description' => 'Diseño, tinte, extensiones, lifting.', 'sort_order' => 6],
            ['name' => 'Depilación', 'icon' => '🌸', 'description' => 'Cera, hilo y láser.', 'sort_order' => 7],
            ['name' => 'Spa y Facial', 'icon' => '🧖', 'description' => 'Faciales, masajes y tratamientos.', 'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            $cat['slug'] = \Illuminate\Support\Str::slug($cat['name']);
            ServiceCategory::firstOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, ['is_active' => true])
            );
        }
    }
}