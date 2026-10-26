<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis citas</h2>
            <a href="{{ route('professional.dashboard') }}" class="text-sm text-teal-600 hover:underline">Dashboard</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" class="mb-4 flex flex-wrap gap-2">
                <input type="date" name="date" value="{{ request('date') }}" class="border-gray-300 rounded-lg text-sm">
                <select name="status" class="border-gray-300 rounded-lg text-sm">
                    <option value="">Todos los estados</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" @selected(request('status')===$st->value)>{{ $st->label() }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-lg">Filtrar</button>
            </form>
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Paciente</th>
                            <th class="px-4 py-3 text-left">Modalidad</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($appointments as $cita)
                            <tr>
                                <td class="px-4 py-3">{{ $cita->date->format('d/m/Y') }} <span class="text-gray-500">{{ \Carbon\Carbon::parse($cita->time)->format('H:i') }}</span></td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $cita->patient->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $cita->patient->document_number ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $cita->modality->label() }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $cita->status->color() }}-100 text-{{ $cita->status->color() }}-800">{{ $cita->status->label() }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('professional.appointments.show', $cita) }}" class="text-teal-600 hover:underline">Gestionar</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay citas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $appointments->links() }}</div>
        </div>
    </div>
</x-app-layout>
