<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Support\CsvExport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $branchScope = $request->user()->branchScope();

        return Expense::with(['category', 'branch', 'user'])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('expense_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('expense_date', '<=', $request->to))
            ->when($request->filled('category_id'), fn ($q) => $q->where('expense_category_id', $request->category_id))
            ->when($branchScope, fn ($q) => $q->where('branch_id', $branchScope))
            ->when(! $branchScope && $request->filled('branch_id'), fn ($q) => $q->where('branch_id', $request->branch_id));
    }

    public function index(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        $expenses = $this->filteredQuery($request)
            ->latest('expense_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/expenses/Index', [
            'expenses' => $expenses,
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'branches' => Branch::active()->when($branchScope, fn ($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
            'filters' => $request->only(['from', 'to', 'category_id', 'branch_id']),
            'total' => $this->filteredQuery($request)->sum('amount'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $expenses = $this->filteredQuery($request)->latest('expense_date')->get();

        return CsvExport::download(
            'egresos-'.now()->format('Y-m-d').'.csv',
            ['Fecha', 'Descripción', 'Categoría', 'Sucursal', 'Método de pago', 'Monto', 'No. de recibo'],
            $expenses->map(fn (Expense $expense) => [
                $expense->expense_date->format('Y-m-d'),
                $expense->description,
                $expense->category?->name,
                $expense->branch?->name ?? '—',
                $expense->payment_method,
                number_format((float) $expense->amount, 2, '.', ''),
                $expense->receipt_number ?? '',
            ]),
        );
    }

    public function create(Request $request): Response
    {
        $branchScope = $request->user()->branchScope();

        return Inertia::render('admin/expenses/Form', [
            'categories' => ExpenseCategory::active()->orderBy('name')->get(),
            'branches' => Branch::active()->when($branchScope, fn ($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,transfer',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($branchScope = $request->user()->branchScope()) {
            $validated['branch_id'] = $branchScope;
        }

        $validated['user_id'] = $request->user()->id;
        Expense::create($validated);

        return redirect()->route('admin.expenses.index')->with('success', 'Egreso registrado.');
    }

    public function edit(Request $request, Expense $expense): Response
    {
        $this->authorizeBranch($request, $expense);
        $branchScope = $request->user()->branchScope();

        return Inertia::render('admin/expenses/Form', [
            'expense' => $expense,
            'categories' => ExpenseCategory::active()->orderBy('name')->get(),
            'branches' => Branch::active()->when($branchScope, fn ($q) => $q->where('id', $branchScope))->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $this->authorizeBranch($request, $expense);

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,transfer',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($branchScope = $request->user()->branchScope()) {
            $validated['branch_id'] = $branchScope;
        }

        $expense->update($validated);

        return redirect()->route('admin.expenses.index')->with('success', 'Egreso actualizado.');
    }

    public function destroy(Request $request, Expense $expense): RedirectResponse
    {
        $this->authorizeBranch($request, $expense);
        $expense->delete();

        return back()->with('success', 'Egreso eliminado.');
    }

    private function authorizeBranch(Request $request, Expense $expense): void
    {
        $branchScope = $request->user()->branchScope();
        abort_if($branchScope && $expense->branch_id !== $branchScope, 403);
    }
}
