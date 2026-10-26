<x-app-layout>
    <x-slot name="header">
        <h2 class="mentea-page-title">Panel super usuario</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="mentea-stat">
                    <div class="mentea-stat-label">Tickets abiertos</div>
                    <div class="mentea-stat-value">{{ $openTickets }}</div>
                </div>
                <div class="mentea-stat">
                    <div class="mentea-stat-label">Auditoría hoy</div>
                    <div class="mentea-stat-value">{{ $auditToday }}</div>
                </div>
                <div class="mentea-stat">
                    <div class="mentea-stat-label">Ajustes</div>
                    <div class="mentea-stat-value">{{ $settingsCount }}</div>
                </div>
                <div class="mentea-stat">
                    <div class="mentea-stat-label">Usuarios</div>
                    <div class="mentea-stat-value">{{ $usersCount }}</div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div class="mentea-card p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-slate-800">Tickets recientes</h3>
                        <a href="{{ route('super.tickets.index') }}" class="text-sm text-teal-700 hover:underline">Ver todos</a>
                    </div>
                    <ul class="divide-y text-sm">
                        @forelse($recentTickets as $t)
                            <li class="py-2 flex justify-between gap-2">
                                <a href="{{ route('super.tickets.show', $t) }}" class="text-teal-700 hover:underline truncate">{{ $t->subject }}</a>
                                <span class="text-slate-400 shrink-0">{{ $t->statusLabel() }}</span>
                            </li>
                        @empty
                            <li class="py-4 text-slate-500">Sin tickets.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="mentea-card p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-slate-800">Auditoría reciente</h3>
                        <a href="{{ route('super.audit.index') }}" class="text-sm text-teal-700 hover:underline">Ver todo</a>
                    </div>
                    <ul class="divide-y text-sm">
                        @forelse($recentAudit as $log)
                            <li class="py-2">
                                <div class="font-medium text-slate-700">{{ $log->action }}</div>
                                <div class="text-xs text-slate-400">{{ $log->user?->name ?? 'Sistema' }} · {{ $log->created_at->diffForHumans() }}</div>
                            </li>
                        @empty
                            <li class="py-4 text-slate-500">Sin registros.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('super.settings.index') }}" class="btn-mentea">Configuración del sistema</a>
                <a href="{{ route('super.tickets.index') }}" class="btn-mentea-outline">Tickets de soporte</a>
                <a href="{{ route('super.audit.index') }}" class="btn-mentea-outline">Auditoría</a>
            </div>
        </div>
    </div>
</x-app-layout>
