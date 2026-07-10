<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'reminders:send {--hours=24 : Horas antes de la cita para enviar recordatorio}';

    protected $description = 'Envía recordatorios de WhatsApp para citas próximas';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $target = Carbon::now()->addHours($hours);

        $startWindow = $target->copy()->subMinutes(30);
        $endWindow = $target->copy()->addMinutes(30);

        $appointments = Appointment::with(['client', 'branch', 'services.service', 'stylist.user'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNull('reminder_sent_at')
            ->whereDate('date', $target->toDateString())
            ->whereTime('start_time', '>=', $startWindow->format('H:i:s'))
            ->whereTime('start_time', '<=', $endWindow->format('H:i:s'))
            ->get();

        $this->info("Encontradas {$appointments->count()} citas para recordar");

        $sent = 0;
        foreach ($appointments as $appointment) {
            if (!$appointment->client?->phone) {
                $this->warn("Cita {$appointment->code} sin teléfono, saltando");
                continue;
            }

            $message = "¡Hola {$appointment->client->name}! 💇‍♀️✨\n\n";
            $message .= "Te recordamos tu cita:\n";
            $message .= "📅 {$appointment->date->format('d/m/Y')} a las " . substr($appointment->start_time, 0, 5) . "\n";
            $serviceName = $appointment->services->first()?->service?->name ?? 'tu servicio';
            $message .= "💇 {$serviceName}\n";
            $message .= "📍 {$appointment->branch?->name}\n\n";
            $message .= "¡Te esperamos! Confirma respondiendo este mensaje. ✅";

            $success = WhatsAppService::sendMessage($appointment->client->phone, $message);

            if ($success) {
                $appointment->update(['reminder_sent_at' => now()]);
                $sent++;
                $this->info("✓ Recordatorio enviado para cita {$appointment->code}");
            } else {
                $this->error("✗ Error enviando recordatorio para {$appointment->code}");
            }
        }

        $this->info("Total enviados: {$sent}");
        return self::SUCCESS;
    }
}