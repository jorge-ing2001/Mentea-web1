@php $p = $professional ?? null; @endphp
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
    <input type="text" name="name" value="{{ old('name', $p->name ?? '') }}" required class="w-full border-gray-300 rounded-lg">
    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad *</label>
        <input type="text" name="specialty" value="{{ old('specialty', $p->specialty ?? '') }}" required list="specialties" class="w-full border-gray-300 rounded-lg">
        <datalist id="specialties">
            <option value="Psicología"><option value="Psiquiatría"><option value="Neuropsicología">
        </datalist>
        @error('specialty') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
        <input type="text" name="profession" value="{{ old('profession', $p->profession ?? '') }}" class="w-full border-gray-300 rounded-lg">
    </div>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
    <input type="email" name="email" value="{{ old('email', $p->email ?? '') }}" required class="w-full border-gray-300 rounded-lg">
    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
        <input type="text" name="phone" value="{{ old('phone', $p->phone ?? '') }}" class="w-full border-gray-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Duración cita (min) *</label>
        <input type="number" name="appointment_duration_minutes" value="{{ old('appointment_duration_minutes', $p->appointment_duration_minutes ?? 45) }}" min="15" max="180" required class="w-full border-gray-300 rounded-lg">
    </div>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
    <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('description', $p->description ?? '') }}</textarea>
</div>
<div class="flex items-center gap-2">
    <input type="checkbox" name="is_available" value="1" id="is_available"
           @checked(old('is_available', $p->is_available ?? true)) class="rounded text-teal-600">
    <label for="is_available" class="text-sm">Disponible para agendar</label>
</div>
