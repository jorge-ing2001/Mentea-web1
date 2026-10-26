<x-app-layout>
    <x-slot name="header">
        <h2 class="mentea-page-title">Nueva solicitud de soporte</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('patient.tickets.store') }}" class="mentea-card p-6 space-y-4">
                @csrf
                <div>
                    <label class="mentea-label">Asunto *</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required class="mentea-input" maxlength="150">
                    @error('subject')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mentea-label">Mensaje *</label>
                    <textarea name="message" rows="5" required class="mentea-input">{{ old('message') }}</textarea>
                    @error('message')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mentea-label">Prioridad</label>
                    <select name="priority" class="mentea-input">
                        <option value="low">Baja</option>
                        <option value="medium" selected>Media</option>
                        <option value="high">Alta</option>
                    </select>
                </div>
                <button type="submit" class="btn-mentea w-full">Enviar</button>
            </form>
        </div>
    </div>
</x-app-layout>
