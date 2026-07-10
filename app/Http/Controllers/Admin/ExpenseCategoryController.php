<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return Inertia::render('admin/expense-categories/Index', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,variable',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        ExpenseCategory::create($validated);

        return back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, ExpenseCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,variable',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $category->update($validated);

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(ExpenseCategory $category): RedirectResponse
    {
        if ($category->expenses()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar una categoría con egresos.']);
        }
        $category->delete();
        return back()->with('success', 'Categoría eliminada.');
    }
}