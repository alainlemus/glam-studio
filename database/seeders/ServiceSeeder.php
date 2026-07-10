<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            'Corte y Peinado' => [
                ['name' => 'Corte Dama', 'price' => 350, 'duration_minutes' => 45],
                ['name' => 'Corte Caballero', 'price' => 250, 'duration_minutes' => 30],
                ['name' => 'Corte Niño', 'price' => 200, 'duration_minutes' => 30],
                ['name' => 'Peinado Evento', 'price' => 600, 'duration_minutes' => 60],
                ['name' => 'Brushing', 'price' => 300, 'duration_minutes' => 45],
            ],
            'Coloración' => [
                ['name' => 'Tinte Raíz', 'price' => 800, 'duration_minutes' => 90],
                ['name' => 'Tinte Completo', 'price' => 1500, 'duration_minutes' => 120],
                ['name' => 'Mechas', 'price' => 1800, 'duration_minutes' => 150],
                ['name' => 'Balayage', 'price' => 2500, 'duration_minutes' => 180],
                ['name' => 'Ombré', 'price' => 2200, 'duration_minutes' => 150],
                ['name' => 'Decoloración', 'price' => 1200, 'duration_minutes' => 120],
            ],
            'Tratamientos Capilares' => [
                ['name' => 'Keratina', 'price' => 3500, 'duration_minutes' => 180],
                ['name' => 'Botox Capilar', 'price' => 2500, 'duration_minutes' => 120],
                ['name' => 'Hidratación Profunda', 'price' => 800, 'duration_minutes' => 60],
                ['name' => 'Alisado Japonés', 'price' => 4500, 'duration_minutes' => 240],
                ['name' => 'Plastificado', 'price' => 1200, 'duration_minutes' => 90],
            ],
            'Uñas' => [
                ['name' => 'Manicure', 'price' => 200, 'duration_minutes' => 45],
                ['name' => 'Pedicure', 'price' => 250, 'duration_minutes' => 60],
                ['name' => 'Gel', 'price' => 450, 'duration_minutes' => 75],
                ['name' => 'Acrílico', 'price' => 600, 'duration_minutes' => 90],
                ['name' => 'Diseño Especial', 'price' => 750, 'duration_minutes' => 120],
            ],
            'Maquillaje' => [
                ['name' => 'Maquillaje Social', 'price' => 800, 'duration_minutes' => 60],
                ['name' => 'Maquillaje Novia', 'price' => 2500, 'duration_minutes' => 90],
                ['name' => 'Maquillaje XV Años', 'price' => 1500, 'duration_minutes' => 75],
            ],
            'Cejas y Pestañas' => [
                ['name' => 'Diseño de Cejas', 'price' => 150, 'duration_minutes' => 30],
                ['name' => 'Tinte Cejas', 'price' => 250, 'duration_minutes' => 30],
                ['name' => 'Lifting Pestañas', 'price' => 700, 'duration_minutes' => 60],
                ['name' => 'Extensiones Pestaña', 'price' => 1200, 'duration_minutes' => 120],
            ],
            'Depilación' => [
                ['name' => 'Ceja', 'price' => 100, 'duration_minutes' => 15],
                ['name' => 'Axila', 'price' => 200, 'duration_minutes' => 30],
                ['name' => 'Bikini', 'price' => 400, 'duration_minutes' => 30],
                ['name' => 'Media Pierna', 'price' => 450, 'duration_minutes' => 45],
                ['name' => 'Pierna Completa', 'price' => 700, 'duration_minutes' => 60],
            ],
            'Spa y Facial' => [
                ['name' => 'Limpieza Facial', 'price' => 800, 'duration_minutes' => 60],
                ['name' => 'Facial Hidratante', 'price' => 1000, 'duration_minutes' => 75],
                ['name' => 'Facial Antiedad', 'price' => 1300, 'duration_minutes' => 90],
                ['name' => 'Masaje Relajante 60min', 'price' => 1200, 'duration_minutes' => 60],
            ],
        ];

        foreach ($services as $categoryName => $items) {
            $category = ServiceCategory::where('name', $categoryName)->first();
            if (!$category) continue;

            foreach ($items as $service) {
                $service['slug'] = \Illuminate\Support\Str::slug($service['name']);
                Service::firstOrCreate(
                    ['name' => $service['name']],
                    array_merge($service, [
                        'service_category_id' => $category->id,
                        'commission_percentage' => 25.00,
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}