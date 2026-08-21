<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
    public function index(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        $incomes = Income::with(['branch', 'sale'])
            ->when($branchScope, fn($q) => $q->where('branch_id', $branchScope))
            ->when($request->filled('from'), fn($q) => $q->whereDate('income_date', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('income_date', '<=', $request->to))
            ->latest('income_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/incomes/Index', [
            'incomes' => $incomes,
            'filters' => $request->only(['from', 'to']),
            'total' => Income::query()
                ->when($branchScope, fn($q) => $q->where('branch_id', $branchScope))
                ->when($request->filled('from'), fn($q) => $q->whereDate('income_date', '>=', $request->from))
                ->when($request->filled('to'), fn($q) => $q->whereDate('income_date', '<=', $request->to))
                ->sum('amount'),
        ]);
    }

    public function destroy(Request $request, Income $income): RedirectResponse
    {
        $branchScope = $request->user()->branchScope();
        abort_if($branchScope && $income->branch_id !== $branchScope, 403);

        $income->delete();
        return back()->with('success', 'Ingreso eliminado.');
    }
}