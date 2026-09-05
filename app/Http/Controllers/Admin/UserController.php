<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Roles manageable from this screen. Stylists are created/edited from
     * the "Estilistas" section since they carry commission/schedule data.
     */
    private const MANAGEABLE_ROLES = [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_RECEPTIONIST];

    public function index(Request $request): Response
    {
        $users = User::with('branch')
            ->whereIn('role', self::MANAGEABLE_ROLES)
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/users/Form', [
            'branches' => Branch::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(self::MANAGEABLE_ROLES)],
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado.');
    }

    public function edit(User $user): Response
    {
        abort_unless(in_array($user->role, self::MANAGEABLE_ROLES, true), 404);

        return Inertia::render('admin/users/Form', [
            'user' => $user,
            'branches' => Branch::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, self::MANAGEABLE_ROLES, true), 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(self::MANAGEABLE_ROLES)],
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        if ($request->user()->id === $user->id && $validated['role'] !== User::ROLE_ADMIN) {
            return back()->withErrors(['role' => 'No puedes quitarte a ti mismo el rol de administrador.']);
        }

        if ($request->user()->id === $user->id && ! $request->boolean('is_active', true)) {
            return back()->withErrors(['is_active' => 'No puedes desactivar tu propia cuenta.']);
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($user->role !== $validated['role']) {
            Audit::record('role_changed', $user, "Cambió el rol de {$user->name} de \"{$user->role}\" a \"{$validated['role']}\".", [
                'role' => ['old' => $user->role, 'new' => $validated['role']],
            ]);
        }
        if ($user->is_active !== $validated['is_active']) {
            $action = $validated['is_active'] ? 'reactivó' : 'desactivó';
            Audit::record('status_changed', $user, "Se {$action} la cuenta de {$user->name}.", [
                'is_active' => ['old' => $user->is_active, 'new' => $validated['is_active']],
            ]);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, self::MANAGEABLE_ROLES, true), 404);

        if ($request->user()->id === $user->id) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        Audit::record('deleted', $user, "Eliminó al usuario {$user->name} ({$user->email}), rol {$user->role}.");

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado.');
    }
}
