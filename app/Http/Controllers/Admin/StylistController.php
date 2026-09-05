<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Stylist;
use App\Models\StylistSchedule;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class StylistController extends Controller
{
    public function index(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        $stylists = Stylist::with(['user', 'branch'])
            ->when($branchScope, fn ($q) => $q->where('branch_id', $branchScope))
            ->when(! $branchScope && $request->branch_id, fn ($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->search, fn ($q) => $q->whereHas('user', fn ($q2) => $q2->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")))
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/stylists/Index', [
            'stylists' => $stylists,
            'branches' => Branch::active()->when($branchScope, fn ($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
            'filters' => $request->only(['search', 'branch_id']),
        ]);
    }

    public function create(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        return Inertia::render('admin/stylists/Form', [
            'branches' => Branch::active()->when($branchScope, fn ($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'branch_id' => 'required|exists:branches,id',
            'specialty' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'base_salary' => 'required|numeric|min:0',
            'service_commission' => 'required|numeric|min:0|max:100',
            'product_commission' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required_with:schedules|integer|between:1,6',
            'schedules.*.start_time' => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time' => 'required_with:schedules|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.active' => 'nullable|boolean',
        ]);

        if ($branchScope = $request->user()->branchScope()) {
            abort_unless((int) $validated['branch_id'] === $branchScope, 403);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_STYLIST,
            'branch_id' => $validated['branch_id'],
            'is_active' => $validated['is_active'],
        ]);

        $stylist = Stylist::create([
            'user_id' => $user->id,
            'branch_id' => $validated['branch_id'],
            'specialty' => $validated['specialty'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'base_salary' => $validated['base_salary'],
            'service_commission' => $validated['service_commission'],
            'product_commission' => $validated['product_commission'],
            'is_active' => $validated['is_active'],
        ]);

        if (isset($validated['schedules'])) {
            foreach ($validated['schedules'] as $schedule) {
                if (! empty($schedule['active'])) {
                    StylistSchedule::create([
                        'stylist_id' => $stylist->id,
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.stylists.index')->with('success', 'Estilista creado.');
    }

    public function show(Request $request, Stylist $stylist): Response
    {
        $this->authorizeBranch($request, $stylist);

        $stylist->load(['user', 'branch', 'schedules', 'commissions' => fn ($q) => $q->latest()->limit(20)]);

        return Inertia::render('admin/stylists/Show', [
            'stylist' => $stylist,
            'pendingCommissions' => $stylist->pendingCommissions(),
        ]);
    }

    public function edit(Request $request, Stylist $stylist): Response
    {
        $this->authorizeBranch($request, $stylist);
        $branchScope = $request->user()->branchScope();

        $stylist->load('schedules', 'user');

        return Inertia::render('admin/stylists/Form', [
            'stylist' => $stylist,
            'branches' => Branch::active()->when($branchScope, fn ($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Stylist $stylist): RedirectResponse
    {
        $this->authorizeBranch($request, $stylist);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$stylist->user_id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'branch_id' => 'required|exists:branches,id',
            'specialty' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'base_salary' => 'required|numeric|min:0',
            'service_commission' => 'required|numeric|min:0|max:100',
            'product_commission' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required_with:schedules|integer|between:1,6',
            'schedules.*.start_time' => 'required_with:schedules|date_format:H:i',
            'schedules.*.end_time' => 'required_with:schedules|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.active' => 'nullable|boolean',
        ]);

        if ($branchScope = $request->user()->branchScope()) {
            abort_unless((int) $validated['branch_id'] === $branchScope, 403);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $stylist->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'branch_id' => $validated['branch_id'],
            'is_active' => $validated['is_active'],
        ]);

        if (! empty($validated['password'])) {
            $stylist->user->update(['password' => Hash::make($validated['password'])]);
        }

        $stylist->update([
            'branch_id' => $validated['branch_id'],
            'specialty' => $validated['specialty'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'base_salary' => $validated['base_salary'],
            'service_commission' => $validated['service_commission'],
            'product_commission' => $validated['product_commission'],
            'is_active' => $validated['is_active'],
        ]);

        if (isset($validated['schedules'])) {
            $stylist->schedules()->delete();
            foreach ($validated['schedules'] as $schedule) {
                if (! empty($schedule['active'])) {
                    StylistSchedule::create([
                        'stylist_id' => $stylist->id,
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.stylists.index')->with('success', 'Estilista actualizado.');
    }

    public function destroy(Request $request, Stylist $stylist): RedirectResponse
    {
        $this->authorizeBranch($request, $stylist);
        $stylist->load('user');
        Audit::record('deleted', $stylist, "Eliminó al estilista {$stylist->user?->name}.");
        $stylist->user->delete();

        return redirect()->route('admin.stylists.index')->with('success', 'Estilista eliminado.');
    }

    private function authorizeBranch(Request $request, Stylist $stylist): void
    {
        $branchScope = $request->user()->branchScope();
        abort_if($branchScope && $stylist->branch_id !== $branchScope, 403);
    }
}
