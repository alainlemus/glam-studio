<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LoyaltyCard;
use App\Models\LoyaltyStamp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoyaltyController extends Controller
{
    public function index(Request $request): Response
    {
        $cards = LoyaltyCard::with(['client', 'stamps' => fn($q) => $q->latest()->limit(5)])
            ->when($request->search, fn($q) => $q->whereHas('client', fn($q2) =>
                $q2->where('name', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%")))
            ->orderByDesc('stamps_current')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/loyalty/Index', [
            'cards' => $cards,
            'filters' => $request->only(['search']),
            'summary' => [
                'total' => LoyaltyCard::count(),
                'completed' => LoyaltyCard::whereColumn('stamps_current', '>=', 'stamps_required')->count(),
                'rewards_claimed' => LoyaltyCard::sum('total_rewards_claimed'),
            ],
        ]);
    }

    public function addStamp(Request $request, LoyaltyCard $card): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:10',
            'notes' => 'nullable|string|max:255',
        ]);

        $qty = $validated['quantity'] ?? 1;
        LoyaltyStamp::create([
            'loyalty_card_id' => $card->id,
            'type' => 'earned',
            'quantity' => $qty,
            'notes' => $validated['notes'] ?? null,
        ]);
        $card->addStamp($qty);

        return back()->with('success', "Se agregaron {$qty} sello(s).");
    }

    public function redeem(LoyaltyCard $card): RedirectResponse
    {
        if (!$card->redeemReward()) {
            return back()->withErrors(['error' => 'La tarjeta no tiene suficientes sellos para canjear.']);
        }

        LoyaltyStamp::create([
            'loyalty_card_id' => $card->id,
            'type' => 'redeemed',
            'quantity' => $card->stamps_required,
            'notes' => 'Recompensa canjeada',
        ]);

        return back()->with('success', '¡Recompensa canjeada!');
    }

    public function storeForClient(Request $request, Client $client): RedirectResponse
    {
        if ($client->loyaltyCard) {
            return back()->withErrors(['error' => 'Este cliente ya tiene tarjeta.']);
        }

        LoyaltyCard::create([
            'client_id' => $client->id,
            'stamps_required' => 10,
            'is_active' => true,
        ]);

        return back()->with('success', 'Tarjeta creada.');
    }
}