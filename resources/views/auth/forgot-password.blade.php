<x-guest-layout>
    <h1 class="text-xl font-semibold text-slate-800 mb-2 text-center">Recuperar contraseña</h1>
    <p class="text-sm text-slate-500 mb-6 text-center">
        Indica tu correo y te enviaremos un enlace para restablecer la contraseña.
    </p>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="flex items-center justify-end mt-6">
            <x-primary-button>Enviar enlace</x-primary-button>
        </div>
    </form>
</x-guest-layout>
