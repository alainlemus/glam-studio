<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MarketingCampaign;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function index(Request $request): Response
    {
        $campaigns = MarketingCampaign::with(['branch', 'service'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/marketing/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/marketing/Form', [
            'branches' => Branch::active()->orderBy('name')->get(),
            'services' => Service::with('category')->active()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:whatsapp,sms,email,promotion',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
            'service_id' => 'nullable|exists:services,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'message_template' => 'nullable|string',
            'target_audience' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,scheduled,active,finished,cancelled',
        ]);

        MarketingCampaign::create($validated);

        return redirect()->route('admin.marketing.index')->with('success', 'Campaña creada.');
    }

    public function edit(MarketingCampaign $campaign): Response
    {
        return Inertia::render('admin/marketing/Form', [
            'campaign' => $campaign,
            'branches' => Branch::active()->orderBy('name')->get(),
            'services' => Service::with('category')->active()->get(),
        ]);
    }

    public function update(Request $request, MarketingCampaign $campaign): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:whatsapp,sms,email,promotion',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
            'service_id' => 'nullable|exists:services,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'message_template' => 'nullable|string',
            'target_audience' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,scheduled,active,finished,cancelled',
        ]);

        $campaign->update($validated);

        return redirect()->route('admin.marketing.index')->with('success', 'Campaña actualizada.');
    }

    public function activate(MarketingCampaign $campaign): RedirectResponse
    {
        $campaign->update(['status' => 'active']);
        return back()->with('success', 'Campaña activada.');
    }

    public function send(MarketingCampaign $campaign): RedirectResponse
    {
        $clients = \App\Models\Client::active()->get();

        $sent = 0;
        foreach ($clients as $client) {
            if (!$client->phone) continue;

            $message = str_replace(
                ['{nombre}', '{faltan}'],
                [$client->name, 'X'],
                $campaign->message_template ?? ''
            );

            \App\Models\ChatMessage::create([
                'client_id' => $client->id,
                'phone' => $client->phone,
                'direction' => 'outgoing',
                'message' => $message,
                'is_bot' => true,
            ]);

            $sent++;
        }

        $campaign->increment('messages_sent', $sent);

        return back()->with('success', "{$sent} mensajes programados (simulado).");
    }

    public function destroy(MarketingCampaign $campaign): RedirectResponse
    {
        $campaign->delete();
        return back()->with('success', 'Campaña eliminada.');
    }
}