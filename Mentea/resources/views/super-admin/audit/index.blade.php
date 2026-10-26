<x-app-layout>
    <x-slot name="header">
        <h2 class="mentea-page-title">Auditoría del sistema</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
            <form method="GET" class="flex gap-2">
                <input type="text" name="action" value="{{ $action }}" placeholder="Filtrar por acción…" class="mentea-input max-w-xs">
                <button type="submit" class="btn-mentea">Buscar</button>
            </form>
            <a href="{{ route('super.audit.export', request()->query()) }}" class="btn-mentea-outline text-sm">Exportar CSV</a>
            </div>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Acción</th>
                            <th>Usuario</th>
                            <th>IP</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-medium">{{ $log->action }}</td>
                                <td>{{ $log->user?->name ?? '—' }}</td>
                                <td class="text-xs text-slate-400">{{ $log->ip_address }}</td>
                                <td><a href="{{ route('super.audit.show', $log) }}" class="text-teal-700 text-sm hover:underline">Detalle</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-8 text-slate-500">Sin registros de auditoría.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
