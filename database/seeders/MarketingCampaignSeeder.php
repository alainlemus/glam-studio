<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\MarketingCampaign;
use App\Models\Service;
use Illuminate\Database\Seeder;

class MarketingCampaignSeeder extends Seeder
{
    public function run(): void
    {
        $service = Service::inRandomOrder()->first();
        $branches = Branch::all();

        MarketingCampaign::create([
            'name' => 'Promoción San Valentín',
            'description' => 'Descuento especial en servicios para parejas',
            'type' => 'whatsapp',
            'start_date' => now()->subDays(7),
            'end_date' => now()->addDays(7),
            'branch_id' => null,
            'service_id' => $service?->id,
            'discount_percentage' => 20.00,
            'message_template' => '¡Hola {nombre}! 💕 Tenemos una promoción especial para ti en San Valentín. Obtén 20% de descuento en tu próximo servicio. ¡Agenda tu cita hoy!',
            'status' => 'active',
            'target_audience' => 500,
            'messages_sent' => 350,
        ]);

        MarketingCampaign::create([
            'name' => 'Bienvenida Nuevos Clientes',
            'description' => 'Mensaje de bienvenida con beneficio para nuevos clientes',
            'type' => 'whatsapp',
            'start_date' => now()->subDays(30),
            'end_date' => null,
            'branch_id' => null,
            'service_id' => null,
            'discount_percentage' => 15.00,
            'message_template' => '¡Bienvenida {nombre} a Salones Belleza! 🎉 Te obsequiamos un 15% de descuento en tu primera visita. Reserva tu cita cuando quieras.',
            'status' => 'active',
            'target_audience' => 1000,
            'messages_sent' => 720,
        ]);

        MarketingCampaign::create([
            'name' => 'Recordatorio Tarjeta de Lealtad',
            'description' => 'Recordatorio a clientes con sellos próximos a completar',
            'type' => 'whatsapp',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'branch_id' => $branches->first()?->id,
            'service_id' => null,
            'discount_percentage' => null,
            'message_template' => '¡{nombre}! Estás a punto de completar tu tarjeta de lealtad. ¡Te faltan solo {faltan} servicios para tu recompensa! 💇‍♀️✨',
            'status' => 'scheduled',
            'target_audience' => 150,
            'messages_sent' => 0,
        ]);
    }
}