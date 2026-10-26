<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agendar con {{ $professional->name }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-6 text-sm text-gray-600">
                <p><strong>{{ $professional->specialty }}</strong> · {{ $professional->profession }}</p>
                <p class="mt-1">{{ $professional->description }}</p>
            </div>

            <form method="POST" action="{{ route('patient.appointments.store') }}" enctype="multipart/form-data" id="book-form" aria-label="Formulario de agendamiento de cita" class="bg-white rounded-xl shadow-sm border p-6 space-y-6">
                @csrf
                <input type="hidden" name="booking_token" value="{{ \Illuminate\Support\Str::uuid() }}">
                <input type="hidden" name="professional_id" value="{{ $professional->id }}">

                @if ($errors->any())
                    <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                    <select name="date" id="date" required class="w-full border-gray-300 rounded-lg">
                        <option value="">Selecciona una fecha</option>
                        @foreach($availableDates as $d)
                            <option value="{{ $d }}" @selected(old('date')===$d)>{{ \Carbon\Carbon::parse($d)->locale('es')->isoFormat('dddd D [de] MMMM') }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora *</label>
                    <div id="slots-container" class="grid grid-cols-3 sm:grid-cols-4 gap-2 min-h-[40px]">
                        <p class="col-span-full text-sm text-gray-400">Selecciona una fecha</p>
                    </div>
                    <input type="hidden" name="time" id="time" value="{{ old('time') }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad *</label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo (opcional)</label>
                    <textarea name="reason" rows="2" class="w-full border-gray-300 rounded-lg">{{ old('reason') }}</textarea>
                </div>

                <div class="border-t pt-4 space-y-4">
                    <p class="text-sm font-medium text-gray-800">Documentos obligatorios (PDF o imagen)</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Orden *</label>
                        <input type="file" name="order_document" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" required
                               class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG o WEBP · máx. 10 MB</p>
                        @error('order_document') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Autorización *</label>
                        <input type="file" name="authorization_document" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" required
                               class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG o WEBP · máx. 10 MB</p>
                        @error('authorization_document') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('patient.appointments.create') }}" class="px-4 py-2 border rounded-lg">Volver</a>
                    <button type="submit" id="submit-btn" class="flex-1 bg-teal-600 text-white py-2.5 rounded-lg hover:bg-teal-700 disabled:opacity-50" disabled>
                        Confirmar cita
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const dateSelect = document.getElementById('date');
        const slotsContainer = document.getElementById('slots-container');
        const timeInput = document.getElementById('time');
        const submitBtn = document.getElementById('submit-btn');
        const slotsUrl = @json(route('patient.appointments.slots', $professional));
        function updateSubmit() {
            submitBtn.disabled = !dateSelect.value || !timeInput.value;
        }
        dateSelect.addEventListener('change', async () => {
            timeInput.value = ''; updateSubmit();
            slotsContainer.innerHTML = '<p class="col-span-full text-sm text-gray-400">Cargando...</p>';
            if (!dateSelect.value) return;
            try {
                const res = await fetch(`${slotsUrl}?date=${dateSelect.value}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                const slots = data.slots || [];
                if (!slots.length) {
                    slotsContainer.innerHTML = '<p class="col-span-full text-sm text-amber-600">Sin horarios disponibles.</p>';
                    return;
                }
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
                slotsContainer.innerHTML = '<p class="col-span-full text-sm text-red-600">Error al cargar horarios.</p>';
            }
        });
    </script>
</x-app-layout>
