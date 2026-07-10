<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = ProductCategory::withCount('products')->orderBy('name')->get();
        return Inertia::render('admin/product-categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        ProductCategory::create($validated);

        return back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $category->update($validated);

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        if ($category->products()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar una categoría con productos.']);
        }
        $category->delete();
        return back()->with('success', 'Categoría eliminada.');
    }
}