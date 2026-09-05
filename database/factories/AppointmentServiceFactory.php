<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'service_id' => Service::factory(),
            'stylist_id' => null,
            'price' => 300,
            'duration_minutes' => 60,
            'commission_percentage' => 25,
            'commission_amount' => 75,
        ];
    }
}
