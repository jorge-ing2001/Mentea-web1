<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Valora tu cita</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-4 text-sm text-gray-600">
                Cita con <strong>{{ $appointment->professional->name }}</strong>
                el {{ $appointment->date->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
            </div>
            <form method="POST" action="{{ route('patient.surveys.store', $appointment) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">¿Cómo calificarías la atención? *</label>
                    <div class="flex gap-3">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="flex flex-col items-center cursor-pointer">
                                <input type="radio" name="rating" value="{{ $i }}" required class="text-teal-600" @checked(old('rating')==$i)>
                                <span class="text-sm mt-1">{{ $i }}</span>
                            </label>
                        @endfor
                    </div>
                    @error('rating') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comentario (opcional)</label>
                    <textarea name="comment" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('comment') }}</textarea>
                </div>
                <button type="submit" class="w-full bg-teal-600 text-white py-2.5 rounded-lg hover:bg-teal-700">Enviar valoración</button>
            </form>
        </div>
    </div>
</x-app-layout>
