<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function index(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        $stocks = ProductStock::with(['product.category', 'branch'])
            ->when($branchScope, fn($q) => $q->where('branch_id', $branchScope))
            ->when(!$branchScope && $request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->low_stock, fn($q) => $q->whereColumn('stock', '<=', 'min_stock'))
            ->when($request->search, fn($q) => $q->whereHas('product', fn($q2) =>
                $q2->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%")))
            ->orderBy('stock')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/inventory/Index', [
            'stocks' => $stocks,
            'branches' => Branch::active()->when($branchScope, fn($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
            'filters' => $request->only(['search', 'branch_id', 'low_stock']),
        ]);
    }

    public function adjust(Request $request, Product $product, Branch $branch): RedirectResponse
    {
        $branchScope = $request->user()->branchScope();
        abort_if($branchScope && $branch->id !== $branchScope, 403);

        $validated = $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'nullable|string|max:255',
        ]);

        $stock = ProductStock::firstOrCreate(
            ['product_id' => $product->id, 'branch_id' => $branch->id],
            ['stock' => 0, 'min_stock' => $product->min_stock]
        );

        $stock->stock += $validated['adjustment'];
        if ($stock->stock < 0) $stock->stock = 0;
        $stock->save();

        return back()->with('success', "Stock ajustado: {$validated['adjustment']} unidades ({$validated['reason']}).");
    }

    public function transfer(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403, 'Solo un administrador puede transferir inventario entre sucursales.');

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $from = ProductStock::where('product_id', $validated['product_id'])
            ->where('branch_id', $validated['from_branch_id'])
            ->first();

        if (!$from || $from->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stock insuficiente en la sucursal de origen.']);
        }

        $from->decrement('stock', $validated['quantity']);

        $to = ProductStock::firstOrCreate(
            ['product_id' => $validated['product_id'], 'branch_id' => $validated['to_branch_id']],
            ['stock' => 0, 'min_stock' => $from->min_stock]
        );
        $to->increment('stock', $validated['quantity']);

        return back()->with('success', "Transferencia de {$validated['quantity']} unidades completada.");
    }
}