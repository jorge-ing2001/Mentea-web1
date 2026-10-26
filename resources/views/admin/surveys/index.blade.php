<x-app-layout>
    <x-slot name="header"><h2 class="mentea-page-title">Encuestas de satisfacción</h2></x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="mentea-stat"><div class="mentea-stat-label">Respuestas</div><div class="mentea-stat-value">{{ $count }}</div></div>
                <div class="mentea-stat"><div class="mentea-stat-label">Promedio</div><div class="mentea-stat-value">{{ $avg ? number_format($avg, 1) : '—' }} / 5</div></div>
                <div class="mentea-stat"><div class="mentea-stat-label">Pendientes (30 d)</div><div class="mentea-stat-value">{{ $pending }}</div></div>
            </div>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr><th>Fecha</th><th>Paciente</th><th>Profesional</th><th>Nota</th><th>Comentario</th></tr>
                    </thead>
                    <tbody>
                        @forelse($surveys as $s)
                            <tr>
                                <td class="text-slate-500">{{ $s->created_at->format('d/m/Y') }}</td>
                                <td>{{ $s->patient?->name }}</td>
                                <td>{{ $s->appointment?->professional?->name }}</td>
                                <td><strong>{{ $s->rating }}</strong> ★</td>
                                <td class="text-sm text-slate-600">{{ $s->comment }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-8 text-slate-500">Sin encuestas aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $surveys->links() }}
        </div>
    </div>
</x-app-layout>
