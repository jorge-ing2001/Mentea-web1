<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reprogramar cita</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-6 text-sm">
                <p><strong>Profesional:</strong> {{ $appointment->professional->name }}</p>
                <p><strong>Actual:</strong> {{ $appointment->date->format('d/m/Y') }} · {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}</p>
                <p class="text-amber-700 mt-2">Solo disponible en las primeras 24 horas desde que agendaste (o si eres admin).</p>
            </div>
            <form method="POST" action="{{ route('patient.appointments.update', $appointment) }}" id="reschedule-form" class="bg-white rounded-xl shadow-sm border p-6 space-y-6">
                @csrf
                @method('PUT')
                @if ($errors->any())
                    <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nueva fecha</label>
                    <select name="date" id="date" required class="w-full border-gray-300 rounded-lg">
                        <option value="">Selecciona</option>
                        @foreach($availableDates as $d)
                            <option value="{{ $d }}" @selected(old('date')===$d)>{{ \Carbon\Carbon::parse($d)->locale('es')->isoFormat('dddd D [de] MMMM') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nueva hora</label>
                    <div id="slots-container" class="grid grid-cols-3 sm:grid-cols-4 gap-2 min-h-[40px]">
                        <p class="col-span-full text-sm text-gray-400">Selecciona una fecha</p>
                    </div>
                    <input type="hidden" name="time" id="time" value="{{ old('time') }}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad</label>
                    <div class="flex gap-4">
                        @foreach($modalities as $mod)
                            <label class="flex items-center gap-2">
                                <input type="radio" name="modality" value="{{ $mod->value }}"
                                    @checked(old('modality', $appointment->modality->value) === $mod->value) class="text-teal-600">
                                <span>{{ $mod->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('patient.appointments.show', $appointment) }}" class="px-4 py-2 border rounded-lg">Cancelar</a>
                    <button type="submit" id="submit-btn" class="flex-1 bg-teal-600 text-white py-2.5 rounded-lg hover:bg-teal-700 disabled:opacity-50" disabled>Guardar nueva fecha</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const dateSelect = document.getElementById('date');
        const slotsContainer = document.getElementById('slots-container');
        const timeInput = document.getElementById('time');
        const submitBtn = document.getElementById('submit-btn');
        const slotsUrl = @json(route('patient.appointments.slots', $appointment->professional));
        function updateSubmit() { submitBtn.disabled = !dateSelect.value || !timeInput.value; }
        dateSelect.addEventListener('change', async () => {
            timeInput.value = ''; updateSubmit();
            slotsContainer.innerHTML = '<p class="col-span-full text-sm text-gray-400">Cargando...</p>';
            if (!dateSelect.value) return;
            try {
                const res = await fetch(`${slotsUrl}?date=${dateSelect.value}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                const slots = data.slots || [];
                if (!slots.length) { slotsContainer.innerHTML = '<p class="col-span-full text-sm text-amber-600">Sin horarios.</p>'; return; }
                slotsContainer.innerHTML = '';
                slots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button'; btn.textContent = slot;
                    btn.className = 'px-3 py-2 rounded-lg border text-sm hover:border-teal-500 hover:bg-teal-50';
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('#slots-container button').forEach(b => b.classList.remove('bg-teal-600','text-white'));
                        btn.classList.add('bg-teal-600','text-white');
                        timeInput.value = slot; updateSubmit();
                    });
                    slotsContainer.appendChild(btn);
                });
            } catch (e) {
                slotsContainer.innerHTML = '<p class="col-span-full text-sm text-red-600">Error al cargar.</p>';
            }
        });
    </script>
</x-app-layout>
