<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-xl font-semibold text-slate-800 mb-1 text-center">Iniciar sesión</h1>
    <p class="text-sm text-slate-500 mb-6 text-center">Accede a tu cuenta Mentea</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-teal-600 shadow-sm focus:ring-teal-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Recordarme</span>
            </label>
        </div>
        <div class="flex items-center justify-between mt-6 gap-3">
            @if (Route::has('password.request'))
                <a class="text-sm text-teal-700 hover:underline" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
            <x-primary-button>Iniciar sesión</x-primary-button>
        </div>
    </form>
    <p class="mt-6 text-center text-sm text-slate-500">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" class="text-teal-700 font-medium hover:underline">Regístrate</a>
    </p>
</x-guest-layout>
