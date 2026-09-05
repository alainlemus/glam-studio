<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Support\CsvExport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncomeController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $branchScope = $request->user()->branchScope();

        return Income::with(['branch', 'sale'])
            ->when($branchScope, fn ($q) => $q->where('branch_id', $branchScope))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('income_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('income_date', '<=', $request->to));
    }

    public function index(Request $request): Response
    {
        $incomes = $this->filteredQuery($request)
            ->latest('income_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/incomes/Index', [
            'incomes' => $incomes,
            'filters' => $request->only(['from', 'to']),
            'total' => $this->filteredQuery($request)->sum('amount'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $incomes = $this->filteredQuery($request)->latest('income_date')->get();

        return CsvExport::download(
            'ingresos-'.now()->format('Y-m-d').'.csv',
            ['Fecha', 'Concepto', 'Origen', 'Sucursal', 'Monto'],
            $incomes->map(fn (Income $income) => [
                $income->income_date->format('Y-m-d'),
                $income->concept,
                $income->source,
                $income->branch?->name ?? '—',
                number_format((float) $income->amount, 2, '.', ''),
            ]),
        );
    }

    public function destroy(Request $request, Income $income): RedirectResponse
    {
        $branchScope = $request->user()->branchScope();
        abort_if($branchScope && $income->branch_id !== $branchScope, 403);

        $income->delete();

        return back()->with('success', 'Ingreso eliminado.');
    }
}
