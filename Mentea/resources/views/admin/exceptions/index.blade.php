<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="mentea-page-title">Horarios excepcionales</h2>
            <a href="{{ route('admin.exceptions.create') }}" class="btn-mentea text-sm">+ Nueva excepción</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
            <p class="text-sm text-slate-600">Festivos, ausencias y bloqueos puntuales. No generan citas disponibles.</p>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr><th>Fecha</th><th>Tipo</th><th>Profesional</th><th>Horario</th><th>Motivo</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($exceptions as $ex)
                            <tr>
                                <td>{{ $ex->date->format('d/m/Y') }}</td>
                                <td>{{ $ex->typeLabel() }}</td>
                                <td>{{ $ex->professional?->name ?? 'Toda la clínica' }}</td>
                                <td>
                                    @if($ex->isFullDay()) Día completo
                                    @else {{ \Carbon\Carbon::parse($ex->time_from)->format('H:i') }} – {{ \Carbon\Carbon::parse($ex->time_to)->format('H:i') }}
                                    @endif
                                </td>
                                <td class="text-sm text-slate-500">{{ $ex->reason }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.exceptions.destroy', $ex) }}" onsubmit="return confirm('¿Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 text-sm hover:underline">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-8 text-slate-500">Sin excepciones.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $exceptions->links() }}
        </div>
    </div>
</x-app-layout>
