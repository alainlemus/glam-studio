<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = ServiceCategory::withCount('services')->orderBy('sort_order')->get();

        return Inertia::render('admin/service-categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        ServiceCategory::create($validated);

        return back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, ServiceCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $category->update($validated);

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(ServiceCategory $category): RedirectResponse
    {
        if ($category->services()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar una categoría con servicios.']);
        }
        $category->delete();
        return back()->with('success', 'Categoría eliminada.');
    }
}