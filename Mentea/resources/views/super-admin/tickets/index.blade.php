<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="mentea-page-title">Tickets y diligenciamientos</h2>
            <a href="{{ route('super.tickets.create') }}" class="btn-mentea text-sm">+ Evento plataforma</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            <p class="text-sm text-slate-600">
                <strong>Soporte:</strong> solicitudes de usuarios.
                <strong>Diligenciamiento:</strong> eventos documentados por el equipo sobre la plataforma.
            </p>
            <form method="GET" class="flex gap-2 flex-wrap">
                <select name="type" class="mentea-input w-auto" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    <option value="support" @selected(($type ?? '')==='support')>Soporte usuario</option>
                    <option value="platform_event" @selected(($type ?? '')==='platform_event')>Diligenciamiento / evento</option>
                </select>
                <select name="status" class="mentea-input w-auto" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    @foreach(['open'=>'Abierto','in_progress'=>'En progreso','resolved'=>'Resuelto','closed'=>'Cerrado'] as $v=>$l)
                        <option value="{{ $v }}" @selected(($status ?? '')===$v)>{{ $l }}</option>
                    @endforeach
                </select>
            </form>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Asunto</th>
                            <th>Usuario</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $t)
                            <tr>
                                <td>{{ $t->id }}</td>
                                <td>
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $t->isPlatformEvent() ? 'bg-amber-50 text-amber-800' : 'bg-teal-50 text-teal-800' }}">
                                        {{ $t->typeLabel() }}
                                    </span>
                                </td>
                                <td><a href="{{ route('super.tickets.show', $t) }}" class="text-teal-700 hover:underline">{{ $t->subject }}</a></td>
                                <td>{{ $t->user?->name }}</td>
                                <td>{{ $t->priorityLabel() }}</td>
                                <td>{{ $t->statusLabel() }}</td>
                                <td class="text-slate-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-slate-500 py-8">No hay registros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
