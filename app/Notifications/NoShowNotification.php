<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NoShowNotification extends Notification
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
            'icon' => 'user-x',
            'color' => 'orange',
            'title' => 'No-show registrado',
            'message' => "{$this->appointment->client?->name} no se presentó a su cita del {$this->appointment->date->format('d/m/Y')} en {$this->appointment->branch?->name}.",
            'url' => "/admin/appointments/{$this->appointment->id}",
        ];
    }
}
