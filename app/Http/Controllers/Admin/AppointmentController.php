<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Service;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        $query = Appointment::with(['client', 'services.service', 'stylist.user', 'branch']);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($branchScope) {
            $query->where('branch_id', $branchScope);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('stylist_id')) {
            $query->where('stylist_id', $request->stylist_id);
        }

        $appointments = $query->orderBy('date')->orderBy('start_time')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('admin/appointments/Index', [
            'appointments' => $appointments,
            'branches' => Branch::active()->when($branchScope, fn($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
            'stylists' => Stylist::with('user')->active()->when($branchScope, fn($q) => $q->where('branch_id', $branchScope))->get(),
            'filters' => $request->only(['date', 'from', 'to', 'status', 'branch_id', 'stylist_id']),
        ]);
    }

    public function calendar(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();
        $from = $request->filled('from') ? Carbon::parse($request->from) : Carbon::now()->startOfWeek();
        $to = $request->filled('to') ? Carbon::parse($request->to) : Carbon::now()->endOfWeek();

        $query = Appointment::with(['client', 'services.service', 'stylist.user', 'branch'])
            ->whereBetween('date', [$from, $to]);

        if ($branchScope) {
            $query->where('branch_id', $branchScope);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('stylist_id')) {
            $query->where('stylist_id', $request->stylist_id);
        }

        $appointments = $query->get();

        return Inertia::render('admin/appointments/Calendar', [
            'appointments' => $appointments,
            'branches' => Branch::active()->when($branchScope, fn($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
            'stylists' => Stylist::with('user')->active()->when($branchScope, fn($q) => $q->where('branch_id', $branchScope))->get(),
            'range' => [
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
            ],
            'filters' => $request->only(['branch_id', 'stylist_id']),
        ]);
    }

    public function create(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        return Inertia::render('admin/appointments/Form', [
            'clients' => Client::orderBy('name')->limit(200)->get(),
            'branches' => Branch::active()->when($branchScope, fn($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
            'services' => Service::with('category')->active()->get(),
            'stylists' => Stylist::with('user')->active()->when($branchScope, fn($q) => $q->where('branch_id', $branchScope))->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'branch_id' => 'required|exists:branches,id',
            'stylist_id' => 'nullable|exists:stylists,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,confirmed',
        ]);

        if ($branchScope = $request->user()->branchScope()) {
            abort_unless((int) $validated['branch_id'] === $branchScope, 403);
        }

        $totalDuration = 0;
        $total = 0;
        $serviceIds = [];

        foreach ($validated['services'] as $item) {
            $service = Service::findOrFail($item['id']);
            $totalDuration += $service->duration_minutes;
            $total += $service->price;
            $serviceIds[] = $service->id;
        }

        $startTime = $validated['start_time'];
        $endTime = date('H:i:s', strtotime($startTime) + $totalDuration * 60);

        $appointment = Appointment::create([
            'client_id' => $validated['client_id'],
            'branch_id' => $validated['branch_id'],
            'stylist_id' => $validated['stylist_id'] ?? null,
            'date' => $validated['date'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $validated['status'],
            'source' => 'admin',
            'total' => $total,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['services'] as $item) {
            $service = Service::findOrFail($item['id']);
            AppointmentService::create([
                'appointment_id' => $appointment->id,
                'service_id' => $service->id,
                'stylist_id' => $validated['stylist_id'] ?? null,
                'price' => $service->price,
                'duration_minutes' => $service->duration_minutes,
                'commission_percentage' => $service->commission_percentage,
                'commission_amount' => $service->price * $service->commission_percentage / 100,
            ]);
        }

        return redirect()->route('admin.appointments.index')->with('success', 'Cita creada.');
    }

    public function show(Request $request, Appointment $appointment): Response
    {
        $this->authorizeBranch($request, $appointment);

        $appointment->load(['client', 'branch', 'stylist.user', 'services.service']);

        return Inertia::render('admin/appointments/Show', [
            'appointment' => $appointment,
        ]);
    }

    public function edit(Request $request, Appointment $appointment): Response
    {
        $this->authorizeBranch($request, $appointment);
        $branchScope = $request->user()->branchScope();

        $appointment->load('services');

        return Inertia::render('admin/appointments/Form', [
            'appointment' => $appointment,
            'clients' => Client::orderBy('name')->limit(200)->get(),
            'branches' => Branch::active()->when($branchScope, fn($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
            'services' => Service::with('category')->active()->get(),
            'stylists' => Stylist::with('user')->active()->when($branchScope, fn($q) => $q->where('branch_id', $branchScope))->get(),
        ]);
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeBranch($request, $appointment);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'branch_id' => 'required|exists:branches,id',
            'stylist_id' => 'nullable|exists:stylists,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ]);

        if ($branchScope = $request->user()->branchScope()) {
            abort_unless((int) $validated['branch_id'] === $branchScope, 403);
        }

        $appointment->update([
            'client_id' => $validated['client_id'],
            'branch_id' => $validated['branch_id'],
            'stylist_id' => $validated['stylist_id'] ?? null,
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Cita actualizada.');
    }

    public function confirm(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeBranch($request, $appointment);
        $appointment->update(['status' => 'confirmed']);
        return back()->with('success', 'Cita confirmada.');
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeBranch($request, $appointment);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['reason'] ?? null,
        ]);

        return back()->with('success', 'Cita cancelada.');
    }

    public function complete(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeBranch($request, $appointment);
        $appointment->update(['status' => 'completed']);
        return back()->with('success', 'Cita marcada como completada.');
    }

    public function noShow(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeBranch($request, $appointment);
        $appointment->update(['status' => 'no_show']);
        $appointment->client?->registerNoShow();

        return back()->with('success', 'Cita marcada como no-show.');
    }

    public function destroy(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeBranch($request, $appointment);
        $appointment->delete();
        return redirect()->route('admin.appointments.index')->with('success', 'Cita eliminada.');
    }

    private function authorizeBranch(Request $request, Appointment $appointment): void
    {
        $branchScope = $request->user()->branchScope();
        abort_if($branchScope && $appointment->branch_id !== $branchScope, 403);
    }
}