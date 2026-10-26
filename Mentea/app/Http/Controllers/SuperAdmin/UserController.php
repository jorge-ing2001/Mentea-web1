<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = User::query()->with('roles')->latest();
        if ($search = $request->string('q')->toString()) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%");
            });
        }
        if ($role = $request->string('role')->toString()) {
            $q->where('role', $role);
        }

        return view('super-admin.users.index', [
            'users' => $q->paginate(20)->withQueryString(),
            'q' => $search ?? '',
            'roleFilter' => $role ?? '',
            'roles' => Role::cases(),
        ]);
    }

    public function create(): View
    {
        return view('super-admin.users.create', [
            'roles' => Role::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'document_type' => ['nullable', 'string', 'in:CC,CE,TI,PA,RC,NIT,OTRO'],
            'document_number' => ['required', 'string', 'max:30', 'unique:users,document_number'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(array_column(Role::cases(), 'value'))],
            'is_active' => ['nullable', 'boolean'],
            'eps' => ['nullable', 'string', 'max:120'],
            'emergency_contact_name' => ['nullable', 'string', 'max:120'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'consent_informed' => ['nullable', 'boolean'],
            'consent_data' => ['nullable', 'boolean'],
            // profesional
            'specialty' => ['nullable', 'string', 'max:120'],
            'profession' => ['nullable', 'string', 'max:120'],
            'appointment_duration_minutes' => ['nullable', 'integer', 'min:15', 'max:180'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'document_type' => $data['document_type'] ?? 'CC',
            'document_number' => $data['document_number'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
            'eps' => $data['eps'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'consent_informed_at' => $request->boolean('consent_informed') ? now() : null,
            'consent_data_at' => $request->boolean('consent_data') ? now() : null,
            'onboarding_completed_at' => $data['role'] === 'patient' ? null : now(),
        ]);

        try {
            $user->syncRoles([$data['role']]);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Throwable) {
            // Spatie puede no estar migrado en algunos entornos
        }

        if ($data['role'] === Role::Professional->value) {
            Professional::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'specialty' => $data['specialty'] ?? 'Psicología Clínica',
                'profession' => $data['profession'] ?? 'Profesional',
                'is_available' => true,
                'appointment_duration_minutes' => $data['appointment_duration_minutes'] ?? 45,
            ]);
        }

        return redirect()
            ->route('super.users.index')
            ->with('success', "Usuario «{$user->name}» creado con rol {$data['role']}.");
    }
}
