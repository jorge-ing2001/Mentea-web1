<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="mentea-page-title">Roles y permisos (Spatie)</h2>
            <a href="{{ route('super.roles.users') }}" class="btn-mentea-outline text-sm">Asignar roles a usuarios</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            <p class="text-sm text-slate-600">
                Los roles se gestionan con <strong>Spatie Laravel Permission</strong>.
                Puedes ajustar los permisos de cada rol sin tocar código.
            </p>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($roles as $role)
                    <div class="mentea-card p-5">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <h3 class="font-semibold text-slate-900">{{ $role->name }}</h3>
                                <p class="text-xs text-slate-500 mt-1">{{ $role->users_count }} usuario(s)</p>
                            </div>
                            <a href="{{ route('super.roles.edit', $role) }}" class="text-sm text-teal-700 hover:underline">Editar permisos</a>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-1">
                            @forelse($role->permissions->take(8) as $perm)
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-teal-50 text-teal-800">{{ $perm->name }}</span>
                            @empty
                                <span class="text-xs text-slate-400">Sin permisos</span>
                            @endforelse
                            @if($role->permissions->count() > 8)
                                <span class="text-[10px] text-slate-400">+{{ $role->permissions->count() - 8 }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
