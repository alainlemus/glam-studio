<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Stylist;
use App\Models\StylistSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@salones.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'phone' => '55 1111 1111',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@salones.com'],
            [
                'name' => 'Gerente General',
                'password' => Hash::make('password'),
                'phone' => '55 2222 2222',
                'role' => User::ROLE_MANAGER,
                'branch_id' => Branch::first()?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $branches = Branch::all();
        $stylistNames = [
            'Sofía Hernández', 'Daniela Torres', 'Andrea Ruiz',
            'Camila Mendoza', 'Valentina Castro', 'Isabella Morales',
            'Ximena Vargas', 'Renata Ríos', 'Mariana Delgado',
            'Paula Salinas', 'Lucía Ortega', 'Emma Guerrero',
        ];

        $specialties = ['Colorista', 'Estilista Senior', 'Cortes', 'Tratamientos', 'Maquillaje', 'Extensiones'];

        foreach ($branches as $branchIndex => $branch) {
            $stylistsForBranch = array_slice($stylistNames, $branchIndex * 3, 3);

            foreach ($stylistsForBranch as $idx => $name) {
                $email = Str::slug($name) . '@salones.com';

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make('password'),
                        'phone' => fake()->numerify('55 #### ####'),
                        'role' => User::ROLE_STYLIST,
                        'branch_id' => $branch->id,
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );

                $stylist = Stylist::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'branch_id' => $branch->id,
                        'specialty' => $specialties[($branchIndex * 3 + $idx) % count($specialties)],
                        'bio' => "Estilista profesional con experiencia en {$specialties[($branchIndex * 3 + $idx) % count($specialties)]}.",
                        'base_salary' => 8000,
                        'service_commission' => 25.00,
                        'product_commission' => 10.00,
                        'is_active' => true,
                    ]
                );

                for ($day = 1; $day <= 6; $day++) {
                    StylistSchedule::firstOrCreate(
                        ['stylist_id' => $stylist->id, 'day_of_week' => $day],
                        [
                            'start_time' => '09:00:00',
                            'end_time' => '19:00:00',
                            'is_active' => true,
                        ]
                    );
                }
            }
        }

        User::firstOrCreate(
            ['email' => 'recepcion@salones.com'],
            [
                'name' => 'Recepción',
                'password' => Hash::make('password'),
                'phone' => '55 3333 3333',
                'role' => User::ROLE_RECEPTIONIST,
                'branch_id' => Branch::first()?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}