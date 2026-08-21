<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Commission;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $user = $request->user();
        $branchFilter = $user->branchScope();

        // Métricas principales
        $appointmentsToday = Appointment::query()
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->whereDate('date', $today)
            ->count();

        $pendingAppointments = Appointment::query()
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->where('status', 'pending')
            ->whereDate('date', '>=', $today)
            ->count();

        $monthlySales = Sale::query()
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startOfMonth, Carbon::now()])
            ->sum('total');

        $lastMonthSales = Sale::query()
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total');

        $monthlyExpenses = Expense::query()
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->whereBetween('expense_date', [$startOfMonth, Carbon::now()])
            ->sum('amount');

        $monthlyCommissions = Commission::query()
            ->where('status', 'pending')
            ->when($branchFilter, fn($q) => $q->whereHas('stylist', fn($q2) => $q2->where('branch_id', $branchFilter)))
            ->sum('amount');

        $totalClients = Client::active()->count();
        $totalStylists = Stylist::active()->count();
        $lowStockCount = ProductStock::query()
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        // Citas próximas
        $upcomingAppointments = Appointment::with(['client', 'services.service', 'stylist.user', 'branch'])
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->upcoming()
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Top estilistas del mes
        $topStylists = Stylist::with('user')
            ->withCount(['sales' => fn($q) => $q->where('status', 'paid')->whereBetween('created_at', [$startOfMonth, Carbon::now()])])
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->orderByDesc('sales_count')
            ->limit(5)
            ->get();

        // Ventas últimos 7 días
        $salesLast7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $total = Sale::query()
                ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
                ->where('status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total');
            $salesLast7Days[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->locale('es')->dayName,
                'total' => (float) $total,
            ];
        }

        // Citas por estatus
        $appointmentsByStatus = Appointment::query()
            ->when($branchFilter, fn($q) => $q->where('branch_id', $branchFilter))
            ->whereMonth('date', Carbon::now()->month)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'appointmentsToday' => $appointmentsToday,
                'pendingAppointments' => $pendingAppointments,
                'monthlySales' => (float) $monthlySales,
                'lastMonthSales' => (float) $lastMonthSales,
                'salesGrowth' => $lastMonthSales > 0 ? (($monthlySales - $lastMonthSales) / $lastMonthSales) * 100 : 0,
                'monthlyExpenses' => (float) $monthlyExpenses,
                'monthlyProfit' => (float) ($monthlySales - $monthlyExpenses - $monthlyCommissions),
                'monthlyCommissions' => (float) $monthlyCommissions,
                'totalClients' => $totalClients,
                'totalStylists' => $totalStylists,
                'lowStockCount' => $lowStockCount,
            ],
            'upcomingAppointments' => $upcomingAppointments,
            'topStylists' => $topStylists,
            'salesLast7Days' => $salesLast7Days,
            'appointmentsByStatus' => $appointmentsByStatus,
            'isAdmin' => $user->isAdmin() || $user->isManager(),
        ]);
    }
}