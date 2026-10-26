<x-guest-layout>
    <h1 class="text-xl font-semibold text-slate-800 mb-2 text-center">Confirmar contraseña</h1>
    <p class="text-sm text-slate-500 mb-6 text-center">
        Esta es una zona segura de la aplicación. Confirma tu contraseña para continuar.
    </p>
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div>
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="flex justify-end mt-6">
            <x-primary-button>Confirmar</x-primary-button>
        </div>
    </form>
</x-guest-layout>
