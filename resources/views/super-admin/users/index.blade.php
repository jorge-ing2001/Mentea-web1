<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="mentea-page-title">Usuarios</h2>
            <a href="{{ route('super.users.create') }}" class="btn-mentea text-sm">+ Crear usuario</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar…" class="mentea-input max-w-xs">
                <select name="role" class="mentea-input w-auto" onchange="this.form.submit()">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->value }}" @selected($roleFilter===$r->value)>{{ $r->label() }}</option>
                    @endforeach
                </select>
                <button class="btn-mentea">Filtrar</button>
            </form>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead><tr><th>Nombre</th><th>Email</th><th>Documento</th><th>Rol</th><th>Activo</th></tr></thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td class="font-medium">{{ $u->name }}</td>
                                <td class="text-sm">{{ $u->email }}</td>
                                <td class="text-sm">{{ $u->document_type }} {{ $u->document_number }}</td>
                                <td><span class="text-xs px-2 py-0.5 rounded-full bg-teal-50 text-teal-800">{{ $u->role?->label() ?? $u->role }}</span></td>
                                <td>{{ $u->is_active ? 'Sí' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
