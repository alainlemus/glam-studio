<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CityController extends Controller
{
    public function index(): Response
    {
        $cities = City::withCount('branches')->orderBy('name')->get();

        return Inertia::render('admin/cities/Index', [
            'cities' => $cities,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/cities/Form', [
            'city' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        City::create($validated);

        return back()->with('success', 'Ciudad creada.');
    }

    public function update(Request $request, City $city): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $city->update($validated);

        return back()->with('success', 'Ciudad actualizada.');
    }

    public function destroy(City $city): RedirectResponse
    {
        if ($city->branches()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar una ciudad con sucursales.']);
        }
        $city->delete();
        return back()->with('success', 'Ciudad eliminada.');
    }
}