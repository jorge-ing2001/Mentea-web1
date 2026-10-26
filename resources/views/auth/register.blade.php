<x-guest-layout>
    <h1 class="text-xl font-semibold text-slate-800 mb-1 text-center">Crear cuenta</h1>
    <p class="text-sm text-slate-500 mb-6 text-center">Únete a Mentea y agenda tu cita</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div>
            <x-input-label for="name" value="Nombre completo" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="document_number" value="Documento de identidad" />
            <x-text-input id="document_number" class="block mt-1 w-full" type="text" name="document_number" :value="old('document_number')" required />
            <x-input-error :messages="$errors->get('document_number')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="phone" value="Teléfono (opcional)" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>
        <div class="mt-4 space-y-2 text-sm text-slate-600">
            <label class="flex items-start gap-2">
                <input type="checkbox" name="consent_informed" value="1" required class="mt-1 rounded border-slate-300 text-teal-600 focus:ring-teal-500" aria-required="true">
                <span>Acepto el <strong>consentimiento informado</strong> para atención en salud mental. *</span>
            </label>
            @error('consent_informed')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
            <label class="flex items-start gap-2">
                <input type="checkbox" name="consent_data" value="1" required class="mt-1 rounded border-slate-300 text-teal-600 focus:ring-teal-500" aria-required="true">
                <span>Autorizo el tratamiento de mis <strong>datos personales</strong> conforme a la Ley 1581 de 2012 (Habeas Data). *</span>
            </label>
            @error('consent_data')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center justify-between mt-6 gap-3">
            <a class="text-sm text-teal-700 hover:underline" href="{{ route('login') }}">¿Ya tienes cuenta?</a>
            <x-primary-button>Registrarse</x-primary-button>
        </div>
    </form>
</x-guest-layout>
