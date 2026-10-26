<x-app-layout>
    <x-slot name="header"><h2 class="mentea-page-title">Crear usuario</h2></x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('super.users.store') }}" class="mentea-card p-6 space-y-4" id="user-create-form">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="mentea-label">Nombre completo *</label>
                        <input name="name" value="{{ old('name') }}" required class="mentea-input">
                        @error('name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mentea-label">Tipo documento</label>
                        <select name="document_type" class="mentea-input">
                            @foreach(['CC','CE','TI','PA','RC','OTRO'] as $t)
                                <option value="{{ $t }}" @selected(old('document_type','CC')===$t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mentea-label">Número documento *</label>
                        <input name="document_number" value="{{ old('document_number') }}" required class="mentea-input">
                        @error('document_number')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mentea-label">Correo *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="mentea-input">
                        @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mentea-label">Teléfono</label>
                        <input name="phone" value="{{ old('phone') }}" class="mentea-input">
                    </div>
                    <div>
                        <label class="mentea-label">Contraseña *</label>
                        <input type="password" name="password" required class="mentea-input">
                        @error('password')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mentea-label">Confirmar contraseña *</label>
                        <input type="password" name="password_confirmation" required class="mentea-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mentea-label">Rol *</label>
                        <select name="role" id="role" class="mentea-input" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->value }}" @selected(old('role')===$r->value)>{{ $r->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="patient-fields" class="space-y-3 border-t pt-4">
                    <p class="text-sm font-semibold text-slate-700">Datos de paciente</p>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="mentea-label">EPS</label><input name="eps" class="mentea-input" value="{{ old('eps') }}"></div>
                        <div><label class="mentea-label">Contacto emergencia</label><input name="emergency_contact_name" class="mentea-input" value="{{ old('emergency_contact_name') }}"></div>
                        <div><label class="mentea-label">Tel. emergencia</label><input name="emergency_contact_phone" class="mentea-input" value="{{ old('emergency_contact_phone') }}"></div>
                    </div>
                    <label class="flex gap-2 text-sm"><input type="checkbox" name="consent_informed" value="1" class="rounded text-teal-600"> Consentimiento informado</label>
                    <label class="flex gap-2 text-sm"><input type="checkbox" name="consent_data" value="1" class="rounded text-teal-600"> Tratamiento de datos (Ley 1581)</label>
                </div>

                <div id="pro-fields" class="space-y-3 border-t pt-4 hidden">
                    <p class="text-sm font-semibold text-slate-700">Datos de profesional</p>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="mentea-label">Especialidad</label><input name="specialty" class="mentea-input" value="{{ old('specialty') }}"></div>
                        <div><label class="mentea-label">Profesión</label><input name="profession" class="mentea-input" value="{{ old('profession') }}"></div>
                        <div><label class="mentea-label">Duración cita (min)</label><input type="number" name="appointment_duration_minutes" class="mentea-input" value="{{ old('appointment_duration_minutes', 45) }}" min="15" max="180"></div>
                    </div>
                </div>

                <label class="flex gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked class="rounded text-teal-600"> Usuario activo</label>
                <button type="submit" class="btn-mentea w-full">Crear usuario</button>
            </form>
        </div>
    </div>
    <script>
        const role = document.getElementById('role');
        const patient = document.getElementById('patient-fields');
        const pro = document.getElementById('pro-fields');
        function toggle() {
            patient.classList.toggle('hidden', role.value !== 'patient');
            pro.classList.toggle('hidden', role.value !== 'professional');
        }
        role.addEventListener('change', toggle);
        toggle();
    </script>
</x-app-layout>
