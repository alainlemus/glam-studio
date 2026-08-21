<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Commission;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Service;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function index(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        $query = Sale::with(['client', 'branch', 'stylist.user', 'user', 'items']);

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($branchScope) {
            $query->where('branch_id', $branchScope);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('admin/sales/Index', [
            'sales' => $sales,
            'branches' => Branch::active()->when($branchScope, fn($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
            'filters' => $request->only(['from', 'to', 'branch_id', 'status']),
            'summary' => [
                'total' => (clone $query)->sum('total'),
                'count' => (clone $query)->count(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        return Inertia::render('admin/sales/Form', [
            'clients' => Client::orderBy('name')->limit(200)->get(),
            'branches' => Branch::active()->when($branchScope, fn($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
            'services' => Service::with('category')->active()->get(),
            'products' => Product::with('category')->active()->get(),
            'stylists' => Stylist::with('user')->active()->when($branchScope, fn($q) => $q->where('branch_id', $branchScope))->get(),
            'appointment' => $request->appointment_id
                ? \App\Models\Appointment::with(['client', 'services.service', 'branch', 'stylist'])->find($request->appointment_id)
                : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'branch_id' => 'required|exists:branches,id',
            'stylist_id' => 'nullable|exists:stylists,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'payment_method' => 'required|in:cash,card,transfer,mixed',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:service,product',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.stylist_id' => 'nullable|exists:stylists,id',
        ]);

        if ($branchScope = $request->user()->branchScope()) {
            abort_unless((int) $validated['branch_id'] === $branchScope, 403);
        }

        $sale = Sale::create([
            'client_id' => $validated['client_id'] ?? null,
            'branch_id' => $validated['branch_id'],
            'stylist_id' => $validated['stylist_id'] ?? null,
            'user_id' => $request->user()->id,
            'appointment_id' => $validated['appointment_id'] ?? null,
            'payment_method' => $validated['payment_method'],
            'discount' => $validated['discount'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'status' => 'paid',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            if ($item['type'] === 'service') {
                $model = Service::findOrFail($item['id']);
                $commissionPct = $model->commission_percentage;
                $stylistId = $item['stylist_id'] ?? $validated['stylist_id'] ?? null;
            } else {
                $model = Product::findOrFail($item['id']);
                $commissionPct = $model->commission_percentage;
                $stylistId = $item['stylist_id'] ?? $validated['stylist_id'] ?? null;
            }

            $unitPrice = $model->price;
            $itemSubtotal = $unitPrice * $item['quantity'];
            $commissionAmount = $itemSubtotal * $commissionPct / 100;
            $subtotal += $itemSubtotal;

            $saleItem = SaleItem::create([
                'sale_id' => $sale->id,
                'itemable_id' => $model->id,
                'itemable_type' => get_class($model),
                'name' => $model->name,
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'subtotal' => $itemSubtotal,
                'commission_percentage' => $commissionPct,
                'commission_amount' => $commissionAmount,
                'stylist_id' => $stylistId,
            ]);

            if ($stylistId && $commissionAmount > 0) {
                Commission::create([
                    'stylist_id' => $stylistId,
                    'sale_id' => $sale->id,
                    'type' => $item['type'],
                    'base_amount' => $itemSubtotal,
                    'percentage' => $commissionPct,
                    'amount' => $commissionAmount,
                    'status' => 'pending',
                ]);
            }

            if ($item['type'] === 'product') {
                $stock = \App\Models\ProductStock::where('product_id', $model->id)
                    ->where('branch_id', $validated['branch_id'])
                    ->first();
                if ($stock) {
                    $stock->decrement('stock', $item['quantity']);
                }
            }
        }

        $sale->update([
            'subtotal' => $subtotal,
            'total' => $subtotal - ($validated['discount'] ?? 0),
        ]);

        \App\Models\Income::create([
            'branch_id' => $validated['branch_id'],
            'sale_id' => $sale->id,
            'concept' => 'Venta ' . $sale->ticket_number,
            'amount' => $sale->total,
            'income_date' => Carbon::today(),
            'source' => 'sale',
        ]);

        if ($sale->appointment_id) {
            $sale->appointment->update(['status' => 'completed']);
        }

        return redirect()->route('admin.sales.show', $sale)->with('success', 'Venta registrada.');
    }

    public function show(Request $request, Sale $sale): Response
    {
        $this->authorizeBranch($request, $sale);

        $sale->load(['client', 'branch', 'stylist.user', 'user', 'items.itemable', 'appointment']);

        return Inertia::render('admin/sales/Show', [
            'sale' => $sale,
        ]);
    }

    public function ticket(Request $request, Sale $sale): Response
    {
        $this->authorizeBranch($request, $sale);

        $sale->load(['client', 'branch', 'items.itemable']);

        return Inertia::render('admin/sales/Ticket', [
            'sale' => $sale,
        ]);
    }

    public function destroy(Request $request, Sale $sale): RedirectResponse
    {
        $this->authorizeBranch($request, $sale);
        $sale->update(['status' => 'cancelled']);
        $sale->commissions()->update(['status' => 'cancelled']);
        return back()->with('success', 'Venta cancelada.');
    }

    private function authorizeBranch(Request $request, Sale $sale): void
    {
        $branchScope = $request->user()->branchScope();
        abort_if($branchScope && $sale->branch_id !== $branchScope, 403);
    }
}