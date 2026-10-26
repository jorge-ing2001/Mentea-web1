<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de cita</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $appointment->professional->name }}</h3>
                        <p class="text-sm text-teal-600">{{ $appointment->professional->specialty }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm rounded-full bg-{{ $appointment->status->color() }}-100 text-{{ $appointment->status->color() }}-800">
                        {{ $appointment->status->label() }}
                    </span>
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Fecha</dt>
                        <dd class="font-medium">{{ $appointment->date->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Hora</dt>
                        <dd class="font-medium">{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Modalidad</dt>
                        <dd class="font-medium">{{ $appointment->modality->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Duración</dt>
                        <dd class="font-medium">{{ $appointment->professional->appointment_duration_minutes }} min</dd>
                    </div>
                </dl>

                @if($appointment->reason)
                    <div>
                        <dt class="text-sm text-gray-500">Motivo</dt>
                        <dd class="text-sm">{{ $appointment->reason }}</dd>
                    </div>
                @endif

                @if($appointment->meet_link)
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800 font-medium">Enlace de videollamada</p>
                        <a href="{{ $appointment->meet_link }}" target="_blank" class="text-teal-600 text-sm break-all hover:underline">
                            {{ $appointment->meet_link }}
                        </a>
                    </div>
                @endif

                <div class="flex gap-3 pt-4 border-t">
                    <a href="{{ route('patient.appointments.index') }}"
                       class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Volver al listado</a>

                    
                @if($appointment->status->value === 'completed' && !$appointment->survey)
                    <a href="{{ route('patient.surveys.create', $appointment) }}"
                       class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg">
                        Valorar esta cita
                    </a>
                @endif

                    
                    <x-appointment-documents :appointment="$appointment" />

                    @can('reschedule', $appointment)
                        <a href="{{ route('patient.appointments.edit', $appointment) }}"
                           class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                            Reprogramar
                        </a>
                    @endcan

                    @can('cancel', $appointment)
                        <form method="POST" action="{{ route('patient.appointments.cancel', $appointment) }}"
                              onsubmit="return confirm('¿Seguro que deseas cancelar esta cita?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                                Cancelar cita
                            </button>
                        </form>
                    @endcan
                </div>

                @if(!$appointment->canBeCancelledByPatient() && $appointment->status->canBeCancelled())
                    <p class="text-xs text-amber-600">
                        Solo puedes cancelar con al menos 5 días de anticipación.
                    </p>
                @endif
            </div>
        </div>
    </div>

@if($appointment->modality?->value === 'virtual')
    <div class="mt-3 text-sm text-slate-600 mentea-card p-3">
        <strong>Videollamada:</strong>
        @if($appointment->meet_link)
            <a href="{{ $appointment->meet_link }}" target="_blank" rel="noopener" class="text-teal-700 underline">{{ $appointment->meet_link }}</a>
        @else
            El enlace se compartirá antes de la cita (correo o panel). No se generan salas ficticias en producción.
        @endif
    </div>
@endif

</x-app-layout>

@if(in_array($appointment->status->value, ['pending','confirmed'], true) && !$appointment->patient_confirmed_at)
<form method="POST" action="{{ route('patient.appointments.confirm', $appointment) }}" class="mt-4">
    @csrf
    <button type="submit" class="btn-mentea">Confirmar asistencia</button>
</form>
@elseif($appointment->patient_confirmed_at)
<p class="text-sm text-teal-700 mt-2">Confirmaste asistencia el {{ $appointment->patient_confirmed_at->format('d/m/Y H:i') }}.</p>
@endif


