<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva solicitud — Lista de espera</h2>
            <a href="{{ route('patient.waitlist.index') }}" class="text-sm text-teal-600 hover:underline">Mis solicitudes</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('patient.waitlist.store') }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-5" id="waitlist-form">
                @csrf
                <p class="text-sm text-gray-600">Indica tus preferencias. Te avisaremos cuando haya un horario compatible (prioridad por orden de llegada).</p>

                @if ($errors->any())
                    <div class="p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium mb-1">Especialidad *</label>
                    <select name="specialty" id="specialty" required class="w-full border-gray-300 rounded-lg">
                        <option value="">Selecciona</option>
                        @foreach($specialties as $s)
                            <option value="{{ $s }}" @selected(old('specialty')===$s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Profesional</label>
                    <select name="professional_id" id="professional_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Cualquier profesional</option>
                        @foreach($professionals as $specialty => $pros)
                            @foreach($pros as $pro)
                                <option value="{{ $pro->id }}" data-specialty="{{ $pro->specialty }}" @selected(old('professional_id')==$pro->id)>
                                    {{ $pro->name }} — {{ $pro->specialty }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Si eliges uno específico, solo se mostrarán los de la especialidad seleccionada.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha inicial *</label>
                        <input type="date" name="date_from" value="{{ old('date_from') }}" min="{{ date('Y-m-d') }}" required class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha final *</label>
                        <input type="date" name="date_to" value="{{ old('date_to') }}" min="{{ date('Y-m-d') }}" required class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Hora desde</label>
                        <input type="time" name="time_from" value="{{ old('time_from', '08:00') }}" class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Hora hasta</label>
                        <input type="time" name="time_to" value="{{ old('time_to', '12:00') }}" class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Modalidad *</label>
                    <div class="flex gap-4">
                        @foreach($modalities as $mod)
                            <label class="flex items-center gap-2">
                                <input type="radio" name="modality" value="{{ $mod->value }}" required
                                    @checked(old('modality', 'presencial') === $mod->value) class="text-teal-600">
                                <span>{{ $mod->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Notas (opcional)</label>
                    <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-teal-600 text-white py-2.5 rounded-lg hover:bg-teal-700 font-medium">
                    Confirmar lista de espera
                </button>
            </form>
        </div>
    </div>
    <script>
        const specialty = document.getElementById('specialty');
        const proSelect = document.getElementById('professional_id');
        function filterPros() {
            const s = specialty.value;
            Array.from(proSelect.options).forEach((opt, i) => {
                if (i === 0) return; // Cualquiera
                const match = !s || opt.dataset.specialty === s;
                opt.hidden = !match;
                if (!match && opt.selected) proSelect.value = '';
            });
        }
        specialty.addEventListener('change', filterPros);
        filterPros();
    </script>
</x-app-layout>
