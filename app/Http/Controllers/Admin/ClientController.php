<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LoyaltyCard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        $clients = Client::with(['loyaltyCard', 'appointments' => fn($q) => $q->latest()->limit(1)])
            ->withCount('appointments')
            ->when($branchScope, fn($q) => $q->whereHas('appointments', fn($q2) => $q2->where('branch_id', $branchScope)))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'blocked') return $q->where('is_blocked', true);
                if ($request->status === 'vip') return $q->where('no_show_count', 0)->has('loyaltyCard');
                if ($request->status === 'active') return $q->where('is_active', true);
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/clients/Form', [
            'client' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:clients,phone',
            'email' => 'nullable|email|max:255',
            'birthday' => 'nullable|date',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $client = Client::create($validated);

        LoyaltyCard::create([
            'client_id' => $client->id,
            'stamps_required' => 10,
            'is_active' => true,
        ]);

        return back()->with('success', 'Cliente creado.');
    }

    public function show(Request $request, Client $client): Response
    {
        $this->authorizeBranch($request, $client);

        $client->load([
            'loyaltyCard.stamps',
            'appointments' => fn($q) => $q->with(['services.service', 'stylist.user', 'branch'])->latest()->limit(20),
            'sales' => fn($q) => $q->with('items')->latest()->limit(20),
        ]);

        return Inertia::render('admin/clients/Show', [
            'client' => $client,
        ]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $this->authorizeBranch($request, $client);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:clients,phone,' . $client->id,
            'email' => 'nullable|email|max:255',
            'birthday' => 'nullable|date',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'is_blocked' => 'boolean',
        ]);

        $client->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'birthday' => $validated['birthday'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_blocked' => $request->boolean('is_blocked', false),
        ]);

        return back()->with('success', 'Cliente actualizado.');
    }

    public function destroy(Request $request, Client $client): RedirectResponse
    {
        $this->authorizeBranch($request, $client);
        $client->delete();
        return back()->with('success', 'Cliente eliminado.');
    }

    private function authorizeBranch(Request $request, Client $client): void
    {
        $branchScope = $request->user()->branchScope();
        if (!$branchScope) {
            return;
        }
        abort_unless($client->appointments()->where('branch_id', $branchScope)->exists(), 403);
    }
}