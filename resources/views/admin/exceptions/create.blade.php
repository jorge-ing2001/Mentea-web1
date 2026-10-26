<x-app-layout>
    <x-slot name="header"><h2 class="mentea-page-title">Nueva excepción de horario</h2></x-slot>
    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.exceptions.store') }}" class="mentea-card p-6 space-y-4">
                @csrf
                <div>
                    <label class="mentea-label">Tipo *</label>
                    <select name="type" id="type" class="mentea-input" required>
                        <option value="block">Bloqueo puntual</option>
                        <option value="absence">Ausencia del profesional</option>
                        <option value="holiday">Festivo / clínica cerrada</option>
                    </select>
                </div>
                <div>
                    <label class="mentea-label">Profesional (vacío = toda la clínica)</label>
                    <select name="professional_id" class="mentea-input">
                        <option value="">— Toda la clínica —</option>
                        @foreach($professionals as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mentea-label">Fecha *</label>
                    <input type="date" name="date" required class="mentea-input" min="{{ date('Y-m-d') }}">
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="full_day" value="1" class="rounded text-teal-600" checked> Día completo
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mentea-label">Desde</label>
                        <input type="time" name="time_from" class="mentea-input">
                    </div>
                    <div>
                        <label class="mentea-label">Hasta</label>
                        <input type="time" name="time_to" class="mentea-input">
                    </div>
                </div>
                <div>
                    <label class="mentea-label">Motivo</label>
                    <input type="text" name="reason" class="mentea-input" maxlength="255" placeholder="Ej. Capacitacion, festivo nacional…">
                </div>
                <button type="submit" class="btn-mentea w-full">Guardar</button>
            </form>
        </div>
    </div>
</x-app-layout>
