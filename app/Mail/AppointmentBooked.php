<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentBooked extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public bool $forStaff = false,
    ) {}

    public function envelope(): Envelope
    {
        $siteName = SiteSetting::current()->site_name ?: config('app.name');
        $client = $this->appointment->client;

        $subject = $this->forStaff
            ? "Nueva cita reservada · {$this->appointment->code}"
            : "Tu cita en {$siteName} está confirmada · {$this->appointment->code}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-booked',
            with: [
                'appointment' => $this->appointment,
                'forStaff' => $this->forStaff,
                'settings' => SiteSetting::current(),
            ],
        );
    }
}
