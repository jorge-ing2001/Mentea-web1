<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Horarios — {{ $professional->name }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.schedules.update', $professional) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                @csrf @method('PUT')
                <p class="text-sm text-gray-500 mb-4">Define los bloques horarios. Se eliminarán los actuales y se crearán los nuevos.</p>
                @php
                    $existing = $professional->schedules->groupBy('day_of_week');
                    $idx = 0;
                @endphp
                @foreach($days as $num => $label)
                    @php $daySchedules = $existing->get($num, collect()); @endphp
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium mb-2">{{ $label }}</h4>
                        @forelse($daySchedules as $sch)
                            <div class="flex gap-2 items-center mb-2">
                                <input type="hidden" name="schedules[{{ $idx }}][day_of_week]" value="{{ $num }}">
                                <input type="time" name="schedules[{{ $idx }}][start_time]" value="{{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }}" class="border-gray-300 rounded-lg text-sm">
                                <span>—</span>
                                <input type="time" name="schedules[{{ $idx }}][end_time]" value="{{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}" class="border-gray-300 rounded-lg text-sm">
                                <label class="flex items-center gap-1 text-sm">
                                    <input type="checkbox" name="schedules[{{ $idx }}][is_active]" value="1" @checked($sch->is_active) class="rounded">
                                    Activo
                                </label>
                            </div>
                            @php $idx++; @endphp
                        @empty
                            <div class="flex gap-2 items-center mb-2">
                                <input type="hidden" name="schedules[{{ $idx }}][day_of_week]" value="{{ $num }}">
                                <input type="time" name="schedules[{{ $idx }}][start_time]" class="border-gray-300 rounded-lg text-sm">
                                <span>—</span>
                                <input type="time" name="schedules[{{ $idx }}][end_time]" class="border-gray-300 rounded-lg text-sm">
                                <label class="flex items-center gap-1 text-sm">
                                    <input type="checkbox" name="schedules[{{ $idx }}][is_active]" value="1" checked class="rounded">
                                    Activo
                                </label>
                            </div>
                            @php $idx++; @endphp
                        @endforelse
                    </div>
                @endforeach
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.professionals.edit', $professional) }}" class="px-4 py-2 border rounded-lg">Volver</a>
                    <button type="submit" class="flex-1 bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700">Guardar horarios</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
