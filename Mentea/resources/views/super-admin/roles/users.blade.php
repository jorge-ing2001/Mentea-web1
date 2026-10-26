<x-app-layout>
    <x-slot name="header">
        <h2 class="mentea-page-title">Usuarios y roles</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar nombre o email…" class="mentea-input max-w-xs">
                <select name="role" class="mentea-input w-auto" onchange="this.form.submit()">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $r)
                        <option value="{{ $r }}" @selected($roleFilter===$r)>{{ $r }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-mentea">Filtrar</button>
            </form>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol actual</th>
                            <th>Asignar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $user->email }}</div>
                                </td>
                                <td>
                                    @foreach($user->roles as $r)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-teal-50 text-teal-800">{{ $r->name }}</span>
                                    @endforeach
                                    @if($user->roles->isEmpty())
                                        <span class="text-xs text-slate-400">{{ $user->role?->value ?? '—' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('super.roles.assign', $user) }}" class="flex gap-2 items-center">
                                        @csrf
                                        <select name="role" class="mentea-input text-sm py-1">
                                            @foreach($roles as $r)
                                                <option value="{{ $r }}" @selected($user->hasRole($r) || $user->role?->value === $r)>{{ $r }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-mentea text-xs py-1.5 px-3">Guardar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
            <a href="{{ route('super.roles.index') }}" class="text-sm text-slate-500 hover:underline">← Volver a roles</a>
        </div>
    </div>
</x-app-layout>
