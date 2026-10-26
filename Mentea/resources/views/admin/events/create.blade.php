<x-app-layout>
    <x-slot name="header">
        <h2 class="mentea-page-title">Registrar diligenciamiento</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.events.store') }}" class="mentea-card p-6 space-y-4">
                @csrf
                <p class="text-sm text-slate-600">Documenta un evento ocurrido en la plataforma (incidencia, cambio, mantenimiento…).</p>
                <div>
                    <label class="mentea-label">Categoría *</label>
                    <select name="category" required class="mentea-input">
                        @foreach($categories as $val => $label)
                            <option value="{{ $val }}" @selected(old('category')===$val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mentea-label">Fecha del evento</label>
                    <input type="datetime-local" name="event_date" value="{{ old('event_date') }}" class="mentea-input">
                </div>
                <div>
                    <label class="mentea-label">Asunto *</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="150" class="mentea-input">
                    @error('subject')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mentea-label">Descripción / diligenciamiento *</label>
                    <textarea name="message" rows="5" required class="mentea-input">{{ old('message') }}</textarea>
                    @error('message')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mentea-label">Prioridad *</label>
                    <select name="priority" class="mentea-input">
                        <option value="low">Baja</option>
                        <option value="medium" selected>Media</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
                <button type="submit" class="btn-mentea w-full">Guardar diligenciamiento</button>
            </form>
        </div>
    </div>
</x-app-layout>
