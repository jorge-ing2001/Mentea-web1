<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agendar cita — Mentea</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <!-- Filtro especialidades -->
            <div class="mb-6 flex flex-wrap gap-2">
                <a href="{{ route('patient.appointments.create') }}"
                   class="px-4 py-2 rounded-full text-sm {{ !$specialty ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 border' }}">
                    Todas
                </a>
                @foreach($specialties as $s)
                    <a href="{{ route('patient.appointments.create', ['specialty' => $s]) }}"
                       class="px-4 py-2 rounded-full text-sm {{ $specialty === $s ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 border' }}">
                        {{ $s }}
                    </a>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($professionals as $pro)
                    <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-14 h-14 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 text-xl font-bold">
                                {{ strtoupper(substr($pro->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $pro->name }}</h3>
                                <p class="text-sm text-teal-600">{{ $pro->specialty }}</p>
                            </div>
                        </div>
                        @if($pro->profession)
                            <p class="text-sm text-gray-500 mb-2">{{ $pro->profession }}</p>
                        @endif
                        @if($pro->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $pro->description }}</p>
                        @endif
                        <div class="mt-auto">
                            <a href="{{ route('patient.appointments.schedule', $pro) }}"
                               class="block w-full text-center bg-teal-600 hover:bg-teal-700 text-white font-medium py-2.5 rounded-lg transition">
                                Elegir horario
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl p-8 text-center text-gray-500">
                        No hay profesionales disponibles en esta especialidad.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
