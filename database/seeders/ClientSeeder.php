<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\LoyaltyCard;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['name' => 'María Fernanda López', 'phone' => '55 1111 2222', 'email' => 'maria.lopez@mail.com'],
            ['name' => 'Ana Patricia Ruiz', 'phone' => '55 2222 3333', 'email' => 'ana.ruiz@mail.com'],
            ['name' => 'Laura Beatriz Sánchez', 'phone' => '33 1111 2222', 'email' => 'laura.sanchez@mail.com'],
            ['name' => 'Sofía Isabel Mendoza', 'phone' => '33 2222 3333', 'email' => 'sofia.mendoza@mail.com'],
            ['name' => 'Daniela Patricia Torres', 'phone' => '81 1111 2222', 'email' => 'daniela.torres@mail.com'],
            ['name' => 'Andrea Michelle Ramírez', 'phone' => '81 2222 3333', 'email' => 'andrea.ramirez@mail.com'],
            ['name' => 'Camila Esperanza Castro', 'phone' => '55 3333 4444', 'email' => 'camila.castro@mail.com'],
            ['name' => 'Valentina Ríos', 'phone' => '33 3333 4444', 'email' => 'valentina.rios@mail.com'],
            ['name' => 'Isabella Morales', 'phone' => '81 3333 4444', 'email' => 'isabella.morales@mail.com'],
            ['name' => 'Ximena Vargas', 'phone' => '44 1111 2222', 'email' => 'ximena.vargas@mail.com'],
            ['name' => 'Renata Delgado', 'phone' => '44 2222 3333', 'email' => 'renata.delgado@mail.com'],
            ['name' => 'Mariana Salinas', 'phone' => '99 1111 2222', 'email' => 'mariana.salinas@mail.com'],
            ['name' => 'Paula Ortega', 'phone' => '99 2222 3333', 'email' => 'paula.ortega@mail.com'],
            ['name' => 'Lucía Guerrero', 'phone' => '55 4444 5555', 'email' => 'lucia.guerrero@mail.com'],
            ['name' => 'Emma Rodríguez', 'phone' => '33 4444 5555', 'email' => 'emma.rodriguez@mail.com'],
        ];

        foreach ($clients as $data) {
            $client = Client::firstOrCreate(
                ['phone' => $data['phone']],
                array_merge($data, [
                    'birthday' => fake()->date(),
                    'is_active' => true,
                ])
            );

            LoyaltyCard::firstOrCreate(
                ['client_id' => $client->id],
                [
                    'code' => 'LOY-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'stamps_required' => 10,
                    'stamps_current' => fake()->numberBetween(0, 8),
                    'is_active' => true,
                ]
            );
        }
    }
}