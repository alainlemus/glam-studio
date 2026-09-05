<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentBooked;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SiteSetting;
use App\Models\Stylist;
use App\Models\StylistSchedule;
use App\Models\Testimonial;
use App\Models\User;
use App\Notifications\NewAppointmentNotification;
use App\Services\WhatsAppService;
use App\Support\NotificationRecipients;
use App\Support\Seo;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class PublicAppointmentController extends Controller
{
    public function create(Request $request): Response
    {
        $branches = Branch::with('city')->active()->get();
        $services = Service::with('category')->active()->get();
        $categories = ServiceCategory::with(['services' => fn ($q) => $q->active()])
            ->active()
            ->orderBy('sort_order')
            ->get();

        $testimonial = Testimonial::active()->inRandomOrder()->first();
        $reviewStats = [
            'count' => Testimonial::active()->count(),
            'average' => round((float) Testimonial::active()->avg('rating'), 1),
        ];
        $appointmentsThisMonth = Appointment::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return Inertia::render('site/Book', [
            'branches' => $branches,
            'services' => $services,
            'categories' => $categories,
            'testimonial' => $testimonial,
            'reviewStats' => $reviewStats,
            'appointmentsThisMonth' => $appointmentsThisMonth,
            'preselected' => [
                'branch_id' => $request->query('branch'),
                'service_id' => $request->query('service'),
            ],
            'seo' => Seo::make(
                'Agenda tu Cita',
                'Reserva tu cita en línea en Glam Studio. Elige sucursal, servicio, estilista y horario disponible en minutos.',
            ),
        ]);
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'stylist_id' => 'nullable|exists:stylists,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            // Permitimos la fecha de hoy. El Book.vue valida que
            // la hora seleccionada sea >= hora actual.
        ]);

        // Normalizar start_time: aceptar "H:i" o "H:i:s"
        $request->merge([
            'start_time' => preg_match('/^\d{2}:\d{2}$/', $request->input('start_time'))
                ? $request->input('start_time').':00'
                : $request->input('start_time'),
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $branch = Branch::findOrFail($validated['branch_id']);
        $date = Carbon::parse($validated['date']);
        $dayOfWeek = $date->dayOfWeek;

        $stylistQuery = Stylist::where('branch_id', $branch->id)
            ->active();

        if (! empty($validated['stylist_id'])) {
            $stylistQuery->where('id', $validated['stylist_id']);
        }

        $stylists = $stylistQuery->get();

        $slots = [];
        foreach ($stylists as $stylist) {
            $schedule = StylistSchedule::where('stylist_id', $stylist->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->first();

            if (! $schedule) {
                continue;
            }

            $slots = array_merge($slots, $this->generateSlotsForStylist(
                $stylist, $schedule, $date, $service
            ));
        }

        return response()->json([
            'slots' => collect($slots)
                ->unique('start')
                ->sortBy('start')
                ->values(),
            'stylists' => $stylists->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->user?->name,
                'specialty' => $s->specialty,
            ])->values(),
        ]);
    }

    private function generateSlotsForStylist(Stylist $stylist, StylistSchedule $schedule, Carbon $date, Service $service): array
    {
        $slots = [];
        $start = Carbon::parse($date->format('Y-m-d').' '.$schedule->start_time);
        $end = Carbon::parse($date->format('Y-m-d').' '.$schedule->end_time);

        $booked = Appointment::where('branch_id', $stylist->branch_id)
            ->whereDate('date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($q) use ($stylist) {
                $q->where('stylist_id', $stylist->id)
                    ->orWhereNull('stylist_id');
            })
            ->get(['start_time', 'end_time']);

        while ($start->copy()->addMinutes($service->duration_minutes)->lte($end)) {
            $slotStart = $start->format('H:i:s');
            $slotEnd = $start->copy()->addMinutes($service->duration_minutes)->format('H:i:s');

            $isPast = $date->isToday() && $start->lt(now());
            $isBooked = $booked->contains(function ($apt) use ($slotStart, $slotEnd) {
                return $apt->start_time < $slotEnd && $apt->end_time > $slotStart;
            });

            if (! $isPast && ! $isBooked) {
                $slots[] = [
                    'start' => $slotStart,
                    'end' => $slotEnd,
                    'stylist_id' => $stylist->id,
                    'stylist_name' => $stylist->user?->name,
                ];
            }

            $start->addMinutes(30);
        }

        return $slots;
    }

    public function store(Request $request)
    {
        // Normalizar start_time ANTES de validar: aceptar "H:i" o "H:i:s"
        $startTimeRaw = $request->input('start_time');
        if ($startTimeRaw && preg_match('/^\d{2}:\d{2}$/', $startTimeRaw)) {
            $request->merge(['start_time' => $startTimeRaw.':00']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'branch_id' => 'required|exists:branches,id',
            'service_id' => 'required|exists:services,id',
            'stylist_id' => 'nullable|exists:stylists,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i:s',
            'notes' => 'nullable|string|max:1000',
        ]);

        $client = Client::firstOrCreate(
            ['phone' => $validated['phone']],
            [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_active' => true,
            ]
        );

        if ($client->email !== $validated['email']) {
            $client->update(['email' => $validated['email']]);
        }

        if (! $client->canBook()) {
            return back()->withErrors(['phone' => 'Lo sentimos, no puedes agendar citas. Contacta a la sucursal.']);
        }

        $service = Service::findOrFail($validated['service_id']);
        $stylistId = $validated['stylist_id'] ?? null;
        $startTime = $validated['start_time'];
        $endTime = date('H:i:s', strtotime($startTime) + $service->duration_minutes * 60);

        $conflict = $this->findConflict(
            $validated['branch_id'],
            $stylistId,
            $validated['date'],
            $startTime,
            $endTime
        );

        if ($conflict) {
            return back()->withErrors([
                'start_time' => 'Ese horario ya no está disponible. Por favor elige otro.',
            ])->withInput();
        }

        $appointment = DB::transaction(function () use ($validated, $client, $service, $stylistId, $startTime, $endTime) {
            $recheck = $this->findConflict(
                $validated['branch_id'],
                $stylistId,
                $validated['date'],
                $startTime,
                $endTime,
                true
            );

            if ($recheck) {
                abort(409, 'Ese horario acaba de ser reservado por otra persona.');
            }

            $appointment = Appointment::create([
                'client_id' => $client->id,
                'branch_id' => $validated['branch_id'],
                'stylist_id' => $stylistId,
                'date' => $validated['date'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'pending',
                'source' => 'web',
                'total' => $service->price,
                'notes' => $validated['notes'] ?? null,
            ]);

            AppointmentService::create([
                'appointment_id' => $appointment->id,
                'service_id' => $service->id,
                'stylist_id' => $stylistId,
                'price' => $service->price,
                'duration_minutes' => $service->duration_minutes,
                'commission_percentage' => $service->commission_percentage,
                'commission_amount' => $service->price * $service->commission_percentage / 100,
            ]);

            return $appointment;
        });

        $whatsappMessage = "¡Hola! Soy {$client->name}, acabo de agendar una cita ({$appointment->code}) para {$service->name} el {$appointment->date->format('d/m/Y')} a las {$appointment->start_time}.";
        $branch = Branch::findOrFail($validated['branch_id']);

        $appointment->load(['client', 'services.service', 'branch', 'stylist.user']);

        Notification::send(
            NotificationRecipients::forBranch($appointment->branch_id),
            new NewAppointmentNotification($appointment),
        );

        $this->sendConfirmationEmails($appointment);

        return Inertia::render('site/BookSuccess', [
            'appointment' => $appointment,
            'whatsappUrl' => WhatsAppService::linkForPhone($branch->whatsapp, $whatsappMessage),
            'seo' => Seo::make(
                'Cita Reservada',
                'Tu cita ha sido reservada exitosamente en Glam Studio.',
                ['noindex' => true],
            ),
        ]);
    }

    private function findConflict(int $branchId, ?int $stylistId, string $date, string $startTime, string $endTime, bool $withLock = false): bool
    {
        $query = Appointment::query()
            ->where('branch_id', $branchId)
            ->whereDate('date', $date)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        if ($stylistId) {
            $query->where('stylist_id', $stylistId);
        }

        if ($withLock) {
            $query->lockForUpdate();
        }

        return $query->exists();
    }

    private function sendConfirmationEmails(Appointment $appointment): void
    {
        try {
            if ($appointment->client?->email) {
                Mail::to($appointment->client->email)
                    ->send(new AppointmentBooked($appointment, forStaff: false));
            }

            $notificationEmail = SiteSetting::current()->notification_email;
            $staffRecipients = $notificationEmail
                ? [$notificationEmail]
                : User::where('role', User::ROLE_ADMIN)->pluck('email')->all();

            if (! empty($staffRecipients)) {
                Mail::to($staffRecipients)
                    ->send(new AppointmentBooked($appointment, forStaff: true));
            }
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de confirmación de cita.', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
