<x-app-layout>
    <x-slot name="header"><h2 class="mentea-page-title">Reportes</h2></x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="mentea-label">Desde</label>
                    <input type="date" name="from" value="{{ $from->toDateString() }}" class="mentea-input">
                </div>
                <div>
                    <label class="mentea-label">Hasta</label>
                    <input type="date" name="to" value="{{ $to->toDateString() }}" class="mentea-input">
                </div>
                <button class="btn-mentea">Aplicar</button>
                <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn-mentea-outline text-sm">Exportar CSV</a>
            </form>

            <div class="grid sm:grid-cols-3 gap-4">
                <div class="mentea-stat"><div class="mentea-stat-label">Encuestas (NPS-ish)</div><div class="mentea-stat-value">{{ $nps['count'] }}</div></div>
                <div class="mentea-stat"><div class="mentea-stat-label">Promedio ★</div><div class="mentea-stat-value">{{ $nps['avg'] ?: '—' }}</div></div>
                <div class="mentea-stat"><div class="mentea-stat-label">Pacientes en riesgo</div><div class="mentea-stat-value">{{ $atRisk->count() }}</div></div>
            </div>

            <div class="mentea-card p-5">
                <h3 class="font-semibold mb-3">Ocupación por profesional</h3>
                <table class="mentea-table">
                    <thead><tr><th>Profesional</th><th>Total</th><th>Completadas</th><th>No-show</th></tr></thead>
                    <tbody>
                        @forelse($byProfessional as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['total'] }}</td>
                                <td>{{ $row['completed'] }}</td>
                                <td>{{ $row['no_show'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-slate-500 py-6">Sin datos en el rango.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mentea-card p-5">
                <h3 class="font-semibold mb-3">Pacientes en riesgo</h3>
                <p class="text-xs text-slate-500 mb-3">≥2 no-shows, ≥3 cancelaciones o bloqueo activo.</p>
                <table class="mentea-table">
                    <thead><tr><th>Paciente</th><th>No-shows</th><th>Cancelaciones</th><th>Bloqueo</th></tr></thead>
                    <tbody>
                        @forelse($atRisk as $p)
                            <tr>
                                <td>{{ $p->name }}<div class="text-xs text-slate-400">{{ $p->email }}</div></td>
                                <td>{{ $p->no_show_count }}</td>
                                <td>{{ $p->cancellation_count }}</td>
                                <td class="text-sm">{{ $p->booking_blocked_until?->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-slate-500 py-6">Ninguno.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
