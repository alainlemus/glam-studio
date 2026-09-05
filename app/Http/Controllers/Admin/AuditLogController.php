<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = AuditLog::with('user')
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->action))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('admin/audit-log/Index', [
            'logs' => $logs,
            'users' => User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_RECEPTIONIST])->orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['action', 'user_id', 'from', 'to']),
        ]);
    }
}
