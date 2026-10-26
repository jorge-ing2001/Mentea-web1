<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Hola, {{ Auth::user()->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('patient.appointments.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Agendar cita</a>
                <a href="{{ route('patient.appointments.index') }}" class="text-sm px-3 py-2 border rounded-lg hover:bg-gray-50">Mis citas</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid md:grid-cols-3 gap-4">
                <a href="{{ route('patient.appointments.create') }}" class="bg-white rounded-xl border p-5 hover:border-teal-300 transition">
                    <div class="text-teal-600 font-semibold">Agendar cita</div>
                    <p class="text-sm text-gray-500 mt-1">Elige especialidad, profesional y horario</p>
                </a>
                <a href="{{ route('patient.appointments.index') }}" class="bg-white rounded-xl border p-5 hover:border-teal-300 transition">
                    <div class="text-teal-600 font-semibold">Mis citas</div>
                    <p class="text-sm text-gray-500 mt-1">Consulta, reprograma o cancela</p>
                </a>
                <a href="{{ route('patient.waitlist.create') }}" class="bg-white rounded-xl border p-5 hover:border-teal-300 transition">
                    <div class="text-teal-600 font-semibold">Lista de espera</div>
                    <p class="text-sm text-gray-500 mt-1">Si no hay horario, te avisamos</p>
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Próximas citas</h3>
                    <a href="{{ route('patient.appointments.index') }}" class="text-sm text-teal-600 hover:underline">Ver todas</a>
                </div>
                @forelse($upcoming as $cita)
                    <a href="{{ route('patient.appointments.show', $cita) }}" class="border-b py-3 flex justify-between items-center block hover:bg-gray-50">
                        <div>
                            <div class="font-medium">{{ $cita->professional->name ?? 'Profesional' }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $cita->date->format('d/m/Y') }} · {{ \Carbon\Carbon::parse($cita->time)->format('H:i') }} · {{ $cita->modality->label() }}
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-{{ $cita->status->color() }}-100 text-{{ $cita->status->color() }}-800">
                            {{ $cita->status->label() }}
                        </span>
                    </a>
                @empty
                    <p class="text-gray-500">
                        No tienes citas próximas.
                        <a href="{{ route('patient.appointments.create') }}" class="text-teal-600 hover:underline">Agendar una cita</a>
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
