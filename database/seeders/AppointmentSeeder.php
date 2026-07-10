<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Client;
use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $stylists = Stylist::with('branch')->get()->groupBy('branch_id');

        if ($clients->isEmpty() || $stylists->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 30; $i++) {
            $stylist = $stylists->flatten()->random();
            $branch = $stylist->branch;
            $client = $clients->random();
            $date = Carbon::now()->addDays(rand(-15, 15))->format('Y-m-d');

            $hour = rand(9, 17);
            $startTime = sprintf('%02d:00:00', $hour);

            $service = Service::inRandomOrder()->first();
            if (!$service) continue;

            $duration = $service->duration_minutes;
            $endTime = date('H:i:s', strtotime($startTime) + $duration * 60);

            $statuses = ['completed', 'completed', 'completed', 'confirmed', 'pending', 'cancelled'];
            $status = $statuses[array_rand($statuses)];

            $appointment = Appointment::create([
                'code' => 'CITA-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'client_id' => $client->id,
                'branch_id' => $branch->id,
                'stylist_id' => $stylist->id,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $status,
                'source' => ['web', 'whatsapp', 'phone'][array_rand(['web', 'whatsapp', 'phone'])],
                'total' => $service->price,
                'deposit' => $status === 'confirmed' ? 100 : 0,
                'notes' => null,
            ]);

            AppointmentService::create([
                'appointment_id' => $appointment->id,
                'service_id' => $service->id,
                'stylist_id' => $stylist->id,
                'price' => $service->price,
                'duration_minutes' => $duration,
                'commission_percentage' => $service->commission_percentage,
                'commission_amount' => $service->price * $service->commission_percentage / 100,
            ]);
        }
    }
}