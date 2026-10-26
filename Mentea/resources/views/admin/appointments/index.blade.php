<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Citas</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-teal-600 hover:underline">Dashboard</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            <form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
                <div>
                    <label class="text-xs text-gray-500">Buscar paciente</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="border-gray-300 rounded-lg text-sm block">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Estado</label>
                    <select name="status" class="border-gray-300 rounded-lg text-sm block">
                        <option value="">Todos</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" @selected(request('status')===$st->value)>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Profesional</label>
                    <select name="professional_id" class="border-gray-300 rounded-lg text-sm block">
                        <option value="">Todos</option>
                        @foreach($professionals as $pro)
                            <option value="{{ $pro->id }}" @selected(request('professional_id')==$pro->id)>{{ $pro->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="border-gray-300 rounded-lg text-sm block">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="border-gray-300 rounded-lg text-sm block">
                </div>
                <button class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-lg">Filtrar</button>
            </form>
            <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Paciente</th>
                            <th class="px-4 py-3 text-left">Profesional</th>
                            <th class="px-4 py-3 text-left">Modalidad</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($appointments as $cita)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $cita->date->format('d/m/Y') }}
                                    <span class="text-gray-500">{{ \Carbon\Carbon::parse($cita->time)->format('H:i') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $cita->patient->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $cita->patient->document_number ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $cita->professional->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $cita->modality->label() }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $cita->status->color() }}-100 text-{{ $cita->status->color() }}-800">
                                        {{ $cita->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.appointments.show', $cita) }}" class="text-teal-600 hover:underline">Gestionar</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay citas con estos filtros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $appointments->links() }}</div>
        </div>
    </div>
</x-app-layout>
