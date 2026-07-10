<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Salary;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommissionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Commission::with(['stylist.user', 'stylist.branch', 'sale', 'appointment']);

        if ($request->filled('stylist_id')) {
            $query->where('stylist_id', $request->stylist_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $commissions = $query->latest()->paginate(20)->withQueryString();

        $summary = [
            'pending' => (clone $query)->where('status', 'pending')->sum('amount'),
            'paid' => (clone $query)->where('status', 'paid')->sum('amount'),
            'count' => (clone $query)->count(),
        ];

        $byStylist = Commission::selectRaw('stylist_id, sum(amount) as total, count(*) as count')
            ->with('stylist.user')
            ->when($request->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->groupBy('stylist_id')
            ->orderByDesc('total')
            ->get();

        return Inertia::render('admin/commissions/Index', [
            'commissions' => $commissions,
            'stylists' => Stylist::with('user')->active()->get(),
            'summary' => $summary,
            'byStylist' => $byStylist,
            'filters' => $request->only(['stylist_id', 'status', 'from', 'to']),
        ]);
    }

    public function pay(Commission $commission): RedirectResponse
    {
        $commission->markAsPaid();
        return back()->with('success', 'Comisión pagada.');
    }

    public function payBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stylist_id' => 'required|exists:stylists,id',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'notes' => 'nullable|string',
        ]);

        $stylist = Stylist::findOrFail($validated['stylist_id']);

        $commissions = Commission::where('stylist_id', $stylist->id)
            ->where('status', 'pending')
            ->whereBetween('created_at', [$validated['from'], $validated['to'] . ' 23:59:59'])
            ->get();

        if ($commissions->isEmpty()) {
            return back()->withErrors(['error' => 'No hay comisiones pendientes para liquidar.']);
        }

        $commissions->each->markAsPaid();

        Salary::create([
            'stylist_id' => $stylist->id,
            'branch_id' => $stylist->branch_id,
            'period_start' => $validated['from'],
            'period_end' => $validated['to'],
            'base_salary' => $stylist->base_salary,
            'commissions_total' => $commissions->sum('amount'),
            'bonuses' => 0,
            'deductions' => 0,
            'total' => $stylist->base_salary + $commissions->sum('amount'),
            'status' => 'paid',
            'paid_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', "Liquidado: {$commissions->count()} comisiones por $" . number_format($commissions->sum('amount'), 2));
    }
}