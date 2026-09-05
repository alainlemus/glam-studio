<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(Request $request): Response
    {
        $services = Service::with('category')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->category_id, fn ($q) => $q->where('service_category_id', $request->category_id))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/services/Index', [
            'services' => $services,
            'categories' => ServiceCategory::orderBy('name')->get(),
            'filters' => $request->only(['search', 'category_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/services/Form', [
            'categories' => ServiceCategory::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'duration_minutes' => 'required|integer|min:5',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Servicio creado.');
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('admin/services/Form', [
            'service' => $service,
            'categories' => ServiceCategory::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'duration_minutes' => 'required|integer|min:5',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ((float) $service->price !== (float) $validated['price']) {
            Audit::record('price_changed', $service, "Cambió el precio de \"{$service->name}\" de $".number_format($service->price, 2).' a $'.number_format($validated['price'], 2).'.', [
                'price' => ['old' => (float) $service->price, 'new' => (float) $validated['price']],
            ]);
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Servicio actualizado.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        Audit::record('deleted', $service, "Eliminó el servicio \"{$service->name}\".");
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Servicio eliminado.');
    }
}
