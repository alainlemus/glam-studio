<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icon' => 'calendar-x',
            'color' => 'red',
            'title' => 'Cita cancelada',
            'message' => "La cita de {$this->appointment->client?->name} el {$this->appointment->date->format('d/m/Y')} en {$this->appointment->branch?->name} fue cancelada.",
            'url' => "/admin/appointments/{$this->appointment->id}",
        ];
    }
}
