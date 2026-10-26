<x-app-layout>
    <x-slot name="header">
        <h2 class="mentea-page-title">Nuevo diligenciamiento / evento</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('super.tickets.store') }}" class="mentea-card p-6 space-y-4">
                @csrf
                <p class="text-sm text-slate-600">Registro de evento sobre la plataforma (no es un ticket de paciente).</p>
                <div>
                    <label class="mentea-label">Categoría *</label>
                    <select name="category" required class="mentea-input">
                        @foreach($categories as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mentea-label">Fecha del evento</label>
                    <input type="datetime-local" name="event_date" class="mentea-input">
                </div>
                <div>
                    <label class="mentea-label">Asunto *</label>
                    <input type="text" name="subject" required class="mentea-input" maxlength="150">
                </div>
                <div>
                    <label class="mentea-label">Descripción *</label>
                    <textarea name="message" rows="5" required class="mentea-input"></textarea>
                </div>
                <div>
                    <label class="mentea-label">Prioridad</label>
                    <select name="priority" class="mentea-input">
                        <option value="low">Baja</option>
                        <option value="medium" selected>Media</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
                <div>
                    <label class="mentea-label">Estado</label>
                    <select name="status" class="mentea-input">
                        <option value="resolved" selected>Resuelto / documentado</option>
                        <option value="open">Abierto</option>
                        <option value="in_progress">En progreso</option>
                        <option value="closed">Cerrado</option>
                    </select>
                </div>
                <button type="submit" class="btn-mentea w-full">Registrar</button>
            </form>
        </div>
    </div>
</x-app-layout>
