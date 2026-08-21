<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('admin/settings/Edit', [
            'settings' => SiteSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'footer_description' => 'nullable|string|max:1000',
            'notification_email' => 'nullable|email|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'remove_logo' => 'boolean',
        ]);

        $settings = SiteSetting::current();

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('branding', 'public');
        } elseif ($request->boolean('remove_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $validated['logo_path'] = null;
        }

        unset($validated['logo'], $validated['remove_logo']);

        $settings->update($validated);

        return redirect()->route('admin.settings.edit')->with('success', 'Configuración actualizada.');
    }
}
