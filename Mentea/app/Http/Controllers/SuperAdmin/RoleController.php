<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\Role as RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::with('permissions')->withCount('users')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'otros';
        });

        return view('super-admin.roles.index', compact('roles', 'permissions'));
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'otros';
        });
        $role->load('permissions');

        return view('super-admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('super.roles.index')
            ->with('success', "Permisos del rol «{$role->name}» actualizados.");
    }

    public function users(Request $request): View
    {
        $q = User::query()->with('roles')->latest();
        if ($search = $request->string('q')->toString()) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($role = $request->string('role')->toString()) {
            $q->role($role);
        }

        return view('super-admin.roles.users', [
            'users' => $q->paginate(20)->withQueryString(),
            'roles' => Role::orderBy('name')->pluck('name'),
            'q' => $search ?? '',
            'roleFilter' => $role ?? '',
        ]);
    }

    public function assignUser(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->syncPrimaryRole($data['role']);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', "Rol de {$user->name} actualizado a «{$data['role']}».");
    }
}
