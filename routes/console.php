<?php

use App\Console\Commands\SendAppointmentReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recordatorios de WhatsApp cada hora
Schedule::command(SendAppointmentReminders::class)
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// Limpiar sesiones inactivas diariamente
Schedule::command('model:prune')->daily();