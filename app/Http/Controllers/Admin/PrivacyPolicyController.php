<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PrivacyPolicyController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('admin/privacy-policy/Edit', [
            'settings' => SiteSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'privacy_policy' => 'required|string|max:20000',
        ]);

        $settings = SiteSetting::current();
        $settings->update([
            'privacy_policy' => $validated['privacy_policy'],
            'privacy_policy_updated_at' => now(),
        ]);

        return redirect()->route('admin.privacy-policy.edit')->with('success', 'Aviso de privacidad actualizado.');
    }
}
