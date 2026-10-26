<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="mentea-page-title">Diligenciamientos de plataforma</h2>
            <a href="{{ route('admin.events.create') }}" class="btn-mentea text-sm">Registrar evento</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            <p class="text-sm text-slate-600">
                Aquí el administrador documenta <strong>eventos e incidencias</strong> de la plataforma
                (mantenimiento, cambios, seguridad, etc.). No son tickets de pacientes.
            </p>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr>
                            <th>Fecha evento</th>
                            <th>Categoría</th>
                            <th>Asunto</th>
                            <th>Registrado por</th>
                            <th>Prioridad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $e)
                            <tr>
                                <td class="whitespace-nowrap text-slate-500">{{ $e->event_date?->format('d/m/Y H:i') ?? $e->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ \App\Models\SupportTicket::categories()[$e->category] ?? $e->category }}</td>
                                <td>
                                    <div class="font-medium">{{ $e->subject }}</div>
                                    <div class="text-xs text-slate-500 line-clamp-2">{{ \Illuminate\Support\Str::limit($e->message, 100) }}</div>
                                </td>
                                <td>{{ $e->user?->name }}</td>
                                <td>{{ $e->priorityLabel() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-8 text-slate-500">Sin diligenciamientos aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $events->links() }}
        </div>
    </div>
</x-app-layout>
