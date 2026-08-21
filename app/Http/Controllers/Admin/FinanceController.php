<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceController extends Controller
{
    public function index(Request $request): Response
    {
        $from = $request->filled('from') ? Carbon::parse($request->from) : Carbon::now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to) : Carbon::now();

        $branchId = $request->user()->branchScope() ?: $request->branch_id;

        $totalIncome = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('income_date', [$from, $to])
            ->sum('amount');

        $totalSales = Sale::where('status', 'paid')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$from, $to])
            ->sum('total');

        $totalExpenses = Expense::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $expensesByCategory = Expense::with('category')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('expense_date', [$from, $to])
            ->selectRaw('expense_category_id, sum(amount) as total')
            ->groupBy('expense_category_id')
            ->get();

        $dailyData = [];
        $days = $to->diffInDays($from) + 1;
        $cursor = $from->copy();

        for ($i = 0; $i < min($days, 60); $i++) {
            $day = $cursor->copy()->addDays($i);
            $income = Income::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereDate('income_date', $day)
                ->sum('amount');
            $expense = Expense::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereDate('expense_date', $day)
                ->sum('amount');
            $dailyData[] = [
                'date' => $day->format('Y-m-d'),
                'income' => (float) $income,
                'expense' => (float) $expense,
                'profit' => (float) ($income - $expense),
            ];
        }

        return Inertia::render('admin/finance/Index', [
            'summary' => [
                'totalIncome' => (float) $totalIncome,
                'totalSales' => (float) $totalSales,
                'totalExpenses' => (float) $totalExpenses,
                'profit' => (float) ($totalIncome - $totalExpenses),
                'margin' => $totalIncome > 0 ? (($totalIncome - $totalExpenses) / $totalIncome) * 100 : 0,
            ],
            'expensesByCategory' => $expensesByCategory,
            'dailyData' => $dailyData,
            'branches' => Branch::active()->when($request->user()->branchScope(), fn($q) => $q->where('id', $branchId))->orderBy('name')->get(),
            'filters' => [
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
                'branch_id' => $branchId,
            ],
        ]);
    }
}