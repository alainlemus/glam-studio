<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $serviceName = $this->appointment->services->first()?->service?->name ?? 'servicio';

        return [
            'icon' => 'calendar-plus',
            'color' => 'blue',
            'title' => 'Nueva cita',
            'message' => "{$this->appointment->client?->name} agendó {$serviceName} el {$this->appointment->date->format('d/m/Y')} en {$this->appointment->branch?->name}.",
            'url' => "/admin/appointments/{$this->appointment->id}",
        ];
    }
}
