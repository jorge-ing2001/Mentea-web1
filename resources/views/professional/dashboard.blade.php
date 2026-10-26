<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agenda — Mentea</h2>
            @if($professional)
                <a href="{{ route('professional.appointments.index') }}" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Todas mis citas</a>
                <a href="{{ route('professional.calendar.index') }}" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Calendario</a>
            @endif
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(!$professional)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-amber-800">
                    Tu cuenta aún no está vinculada a un perfil profesional. Contacta al administrador.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border p-5">
                        <div class="text-sm text-gray-500">Citas hoy</div>
                        <div class="text-3xl font-bold text-teal-600">{{ $todayAppointments->count() }}</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border p-5">
                        <div class="text-sm text-gray-500">Esta semana</div>
                        <div class="text-3xl font-bold text-teal-600">{{ $weekCount }}</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border p-5">
                        <div class="text-sm text-gray-500">Pendientes de confirmar</div>
                        <div class="text-3xl font-bold text-amber-600">{{ $pendingCount }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="font-medium mb-4">Citas de hoy — {{ $professional->name }}</h3>
                    @forelse($todayAppointments as $cita)
                        <div class="border-b py-3 flex justify-between items-center">
                            <div>
                                <div class="font-medium">{{ $cita->patient->name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($cita->time)->format('H:i') }} · {{ $cita->modality->label() }}
                                    @if($cita->patient->phone) · {{ $cita->patient->phone }} @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $cita->status->color() }}-100 text-{{ $cita->status->color() }}-800">
                                    {{ $cita->status->label() }}
                                </span>
                                <a href="{{ route('professional.appointments.show', $cita) }}" class="text-sm text-teal-600 hover:underline">Ver</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No tienes citas programadas para hoy.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
