<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller
{
    public function index(): Response
    {
        $testimonials = Testimonial::orderBy('sort_order')->orderByDesc('created_at')->get();

        return Inertia::render('admin/testimonials/Index', [
            'testimonials' => $testimonials,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/testimonials/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('testimonials', 'public');
        }

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonio creado.');
    }

    public function edit(Testimonial $testimonial): Response
    {
        return Inertia::render('admin/testimonials/Form', [
            'testimonial' => $testimonial,
        ]);
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $this->validated($request);

        if ($request->hasFile('photo')) {
            if ($testimonial->photo_path) {
                Storage::disk('public')->delete($testimonial->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('testimonials', 'public');
        }

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonio actualizado.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        if ($testimonial->photo_path) {
            Storage::disk('public')->delete($testimonial->photo_path);
        }
        $testimonial->delete();

        return back()->with('success', 'Testimonio eliminado.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'quote' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'photo' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        unset($validated['photo']);

        return $validated;
    }
}
