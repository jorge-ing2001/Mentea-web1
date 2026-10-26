<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel Administrador — Mentea</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.professionals.index') }}" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Profesionales</a>
                <a href="{{ route('admin.appointments.index') }}" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Citas</a>
                <a href="{{ route('admin.calendar.index') }}" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Calendario</a>
                <a href="{{ route('admin.waitlist.index') }}" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Lista de espera</a>
            </div>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="text-sm text-gray-500">Pacientes</div>
                    <div class="text-3xl font-bold text-teal-600">{{ $totalPatients }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="text-sm text-gray-500">Profesionales activos</div>
                    <div class="text-3xl font-bold text-teal-600">{{ $totalProfessionals }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="text-sm text-gray-500">Total citas</div>
                    <div class="text-3xl font-bold text-teal-600">{{ $totalAppointments }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="text-sm text-gray-500">Citas hoy</div>
                    <div class="text-3xl font-bold text-teal-600">{{ $appointmentsToday }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <h3 class="font-medium mb-3">Por estado</h3>
                    <ul class="space-y-2 text-sm">
                        @foreach(['pending'=>'Pendiente','confirmed'=>'Confirmada','cancelled'=>'Cancelada','completed'=>'Completada','no_show'=>'No asistió'] as $key => $label)
                            <li class="flex justify-between">
                                <span>{{ $label }}</span>
                                <span class="font-semibold">{{ $byStatus[$key] ?? 0 }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-medium">Próximas citas</h3>
                        <a href="{{ route('admin.appointments.index') }}" class="text-sm text-teal-600 hover:underline">Ver todas</a>
                    </div>
                    @forelse($upcoming as $cita)
                        <a href="{{ route('admin.appointments.show', $cita) }}" class="border-b py-2 flex justify-between text-sm hover:bg-gray-50 block px-1">
                            <div>
                                <span class="font-medium">{{ $cita->patient->name ?? '—' }}</span>
                                <span class="text-gray-500"> · {{ $cita->professional->name ?? '' }}</span>
                            </div>
                            <div class="text-gray-600">
                                {{ $cita->date->format('d/m') }} {{ \Carbon\Carbon::parse($cita->time)->format('H:i') }}
                            </div>
                        </a>
                    @empty
                        <p class="text-gray-500 text-sm">No hay citas próximas. <a href="{{ route('admin.professionals.index') }}" class="text-teal-600">Gestionar profesionales</a></p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
