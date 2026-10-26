<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestionar cita #{{ $appointment->id }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Paciente</div>
                        <div class="font-medium">{{ $appointment->patient->name }}</div>
                        <div class="text-xs text-gray-500">{{ $appointment->patient->email }} · {{ $appointment->patient->document_number }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Profesional</div>
                        <div class="font-medium">{{ $appointment->professional->name }}</div>
                        <div class="text-xs text-gray-500">{{ $appointment->professional->specialty }}</div>
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
                        <div class="text-gray-500">Estado actual</div>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $appointment->status->color() }}-100 text-{{ $appointment->status->color() }}-800">
                            {{ $appointment->status->label() }}
                        </span>
                    </div>
                    @if($appointment->meet_link)
                        <div>
                            <div class="text-gray-500">Meet</div>
                            <a href="{{ $appointment->meet_link }}" target="_blank" class="text-teal-600 text-sm break-all">{{ $appointment->meet_link }}</a>
                        </div>
                    @endif
                </div>
                @if($appointment->reason)
                    <div class="text-sm"><span class="text-gray-500">Motivo:</span> {{ $appointment->reason }}</div>
                @endif
                <x-appointment-documents :appointment="$appointment" />

                @if($appointment->notes)
                    <div class="text-sm"><span class="text-gray-500">Notas:</span> {{ $appointment->notes }}</div>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.appointments.status', $appointment) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                @csrf @method('PATCH')
                <h3 class="font-medium">Cambiar estado</h3>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Nuevo estado</label>
                    <select name="status" required class="w-full border-gray-300 rounded-lg">
                        @foreach(\App\Enums\AppointmentStatus::cases() as $st)
                            <option value="{{ $st->value }}" @selected($appointment->status === $st)>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Notas internas</label>
                    <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg">{{ old('notes', $appointment->notes) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Motivo de cancelación (si aplica)</label>
                    <input type="text" name="cancellation_reason" value="{{ old('cancellation_reason') }}" class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.appointments.index') }}" class="px-4 py-2 border rounded-lg">Volver</a>
                    <button type="submit" class="flex-1 bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
