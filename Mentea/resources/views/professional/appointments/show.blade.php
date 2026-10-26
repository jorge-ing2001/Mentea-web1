<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cita #{{ $appointment->id }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            <div class="bg-white rounded-xl shadow-sm border p-6 space-y-3 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-gray-500">Paciente</div>
                        <div class="font-medium text-base">{{ $appointment->patient->name }}</div>
                        <div class="text-xs text-gray-500">{{ $appointment->patient->email }}</div>
                        @if($appointment->patient->phone)
                            <div class="text-xs text-gray-500">{{ $appointment->patient->phone }}</div>
                        @endif
                    </div>
                    <div>
                        <div class="text-gray-500">Fecha y hora</div>
                        <div class="font-medium">{{ $appointment->date->format('d/m/Y') }} · {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Modalidad</div>
                        <div class="font-medium">{{ $appointment->modality->label() }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Estado</div>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $appointment->status->color() }}-100 text-{{ $appointment->status->color() }}-800">{{ $appointment->status->label() }}</span>
                    </div>
                </div>
                @if($appointment->reason)
                    <div><span class="text-gray-500">Motivo:</span> {{ $appointment->reason }}</div>
                @endif
                @if($appointment->meet_link)
                    <div class="p-2 bg-blue-50 rounded">
                        <a href="{{ $appointment->meet_link }}" target="_blank" class="text-teal-600 break-all">{{ $appointment->meet_link }}</a>
                    </div>
                @endif
            </div>

            <x-appointment-documents :appointment="$appointment" />

            @if(in_array($appointment->status->value, ['pending', 'confirmed']))
                <form method="POST" action="{{ route('professional.appointments.status', $appointment) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                    @csrf @method('PATCH')
                    <h3 class="font-medium">Actualizar estado</h3>
                    <div>
                        <select name="status" required class="w-full border-gray-300 rounded-lg">
                            <option value="confirmed" @selected($appointment->status->value==='confirmed')>Confirmada</option>
                            <option value="completed">Completada</option>
                            <option value="no_show">No asistió</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Notas clínicas (internas)</label>
                        <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('notes', $appointment->notes) }}</textarea>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('professional.appointments.index') }}" class="px-4 py-2 border rounded-lg">Volver</a>
                        <button type="submit" class="flex-1 bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700">Guardar</button>
                    </div>
                </form>
            @else
                <a href="{{ route('professional.appointments.index') }}" class="inline-block px-4 py-2 border rounded-lg">Volver al listado</a>
            @endif
        </div>
    </div>
</x-app-layout>

{{-- Historia clínica mínima (solo profesional / staff) --}}
@can('viewClinicalNotes', $appointment)
<div class="mentea-card p-5 mt-6">
    <h3 class="font-semibold text-slate-800 mb-2">Historia clínica (notas confidenciales)</h3>
    <p class="text-xs text-slate-500 mb-3">Visible solo para el profesional asignado y administradores.</p>
    @if($appointment->clinical_notes)
        <div class="text-sm whitespace-pre-wrap bg-slate-50 rounded-lg p-3 mb-3">{{ $appointment->clinical_notes }}</div>
        <p class="text-xs text-slate-400">Actualizado {{ $appointment->clinical_notes_updated_at?->format('d/m/Y H:i') }}</p>
    @endif
    <form method="POST" action="{{ route('professional.clinical.store', $appointment) }}" class="space-y-3 mt-3">
        @csrf

        <input type="hidden" name="status" value="{{ $appointment->status->value }}">
        <label class="mentea-label" for="clinical_notes">Notas clínicas</label>
        <textarea id="clinical_notes" name="clinical_notes" rows="5" class="mentea-input" aria-label="Notas clínicas confidenciales">{{ old('clinical_notes', $appointment->clinical_notes) }}</textarea>
        <button type="submit" class="btn-mentea text-sm">Guardar notas</button>
    </form>
</div>
@endcan

