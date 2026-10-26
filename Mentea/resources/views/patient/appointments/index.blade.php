<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis citas — Mentea</h2>
            <a href="{{ route('patient.appointments.create') }}"
               class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Agendar cita
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profesional</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modalidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($appointments as $cita)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $cita->date->format('d/m/Y') }}
                                    <span class="text-gray-500">{{ \Carbon\Carbon::parse($cita->time)->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="font-medium">{{ $cita->professional->name ?? '—' }}</div>
                                    <div class="text-gray-500 text-xs">{{ $cita->professional->specialty ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $cita->modality->label() }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $cita->status->color() }}-100 text-{{ $cita->status->color() }}-800">
                                        {{ $cita->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('patient.appointments.show', $cita) }}" class="text-teal-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No tienes citas registradas.
                                    <a href="{{ route('patient.appointments.create') }}" class="text-teal-600 hover:underline">Agendar la primera</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $appointments->links() }}</div>
        </div>
    </div>
</x-app-layout>
