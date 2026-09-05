<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\City;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function index(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        $branches = Branch::with('city')
            ->when($branchScope, fn ($q) => $q->where('id', $branchScope))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('address', 'like', "%{$request->search}%"))
            ->when($request->city_id, fn ($q) => $q->where('city_id', $request->city_id))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/branches/Index', [
            'branches' => $branches,
            'cities' => City::orderBy('name')->get(),
            'filters' => $request->only(['search', 'city_id']),
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403, 'Solo un administrador puede crear sucursales.');

        return Inertia::render('admin/branches/Form', [
            'cities' => City::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403, 'Solo un administrador puede crear sucursales.');

        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'opening_days' => 'nullable|array',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Branch::create($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Sucursal creada correctamente.');
    }

    public function show(Request $request, Branch $branch): Response
    {
        $this->authorizeBranch($request, $branch);

        $branch->load(['city', 'stylists.user', 'productStocks.product']);

        return Inertia::render('admin/branches/Show', [
            'branch' => $branch,
        ]);
    }

    public function edit(Request $request, Branch $branch): Response
    {
        $this->authorizeBranch($request, $branch);

        return Inertia::render('admin/branches/Form', [
            'branch' => $branch,
            'cities' => City::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);

        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'opening_days' => 'nullable|array',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Sucursal actualizada.');
    }

    public function destroy(Request $request, Branch $branch): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403, 'Solo un administrador puede eliminar sucursales.');
        Audit::record('deleted', $branch, "Eliminó la sucursal \"{$branch->name}\".");
        $branch->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Sucursal eliminada.');
    }

    private function authorizeBranch(Request $request, Branch $branch): void
    {
        $branchScope = $request->user()->branchScope();
        abort_if($branchScope && $branch->id !== $branchScope, 403);
    }
}
