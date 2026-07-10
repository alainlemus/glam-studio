<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'city' => 'Ciudad de México',
                'name' => 'Salones Belleza Polanco',
                'address' => 'Av. Presidente Masaryk 123, Polanco',
                'phone' => '55 1234 5678',
                'whatsapp' => '5215512345678',
                'email' => 'polanco@salones.com',
                'manager_name' => 'Ana Martínez',
                'opening_time' => '09:00:00',
                'closing_time' => '21:00:00',
                'description' => 'Nuestra sucursal insignia en Polanco, con los mejores estilistas y ambiente premium.',
            ],
            [
                'city' => 'Ciudad de México',
                'name' => 'Salones Belleza Roma Norte',
                'address' => 'Av. Álvaro Obregón 200, Roma Norte',
                'phone' => '55 8765 4321',
                'whatsapp' => '5215587654321',
                'email' => 'roma@salones.com',
                'manager_name' => 'Carlos López',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'description' => 'Estilo bohemio y atención personalizada en el corazón de la Roma.',
            ],
            [
                'city' => 'Guadalajara',
                'name' => 'Salones Belleza Andares',
                'address' => 'Boulevard Puerta de Hierro 1234, Andares',
                'phone' => '33 1234 5678',
                'whatsapp' => '523312345678',
                'email' => 'andares@salones.com',
                'manager_name' => 'María González',
                'opening_time' => '09:00:00',
                'closing_time' => '21:00:00',
                'description' => 'Tu destino de belleza en Guadalajara, dentro de Andares.',
            ],
            [
                'city' => 'Monterrey',
                'name' => 'Salones Belleza San Pedro',
                'address' => 'Av. Vasconcelos 100, San Pedro Garza García',
                'phone' => '81 1234 5678',
                'whatsapp' => '528112345678',
                'email' => 'sanpedro@salones.com',
                'manager_name' => 'Roberto Hernández',
                'opening_time' => '09:00:00',
                'closing_time' => '21:00:00',
                'description' => 'Elegancia y exclusividad en San Pedro.',
            ],
            [
                'city' => 'Querétaro',
                'name' => 'Salones Belleza Centro Sur',
                'address' => 'Av. Bernardo Quintana 500, Centro Sur',
                'phone' => '44 1234 5678',
                'whatsapp' => '524412345678',
                'email' => 'centrosur@salones.com',
                'manager_name' => 'Laura Ramírez',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'description' => 'Servicios de belleza de alta calidad en Querétaro.',
            ],
        ];

        foreach ($branches as $data) {
            $city = City::where('name', $data['city'])->first();
            if (!$city) continue;

            unset($data['city']);
            $data['city_id'] = $city->id;
            $data['opening_days'] = [1, 2, 3, 4, 5, 6];
            $data['is_active'] = true;
            $data['slug'] = Str::slug($data['name']);

            Branch::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}