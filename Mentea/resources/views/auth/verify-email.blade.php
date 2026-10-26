<x-guest-layout>
    <h1 class="text-xl font-semibold text-slate-800 mb-2 text-center">Verifica tu correo</h1>
    <p class="text-sm text-slate-500 mb-6 text-center">
        Gracias por registrarte. Antes de continuar, confirma tu correo con el enlace que te enviamos.
        Si no lo recibiste, podemos reenviarlo.
    </p>
    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 alert-success text-sm">
            Se ha enviado un nuevo enlace de verificación a tu correo.
        </div>
    @endif
    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Reenviar correo de verificación</x-primary-button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-slate-600 hover:text-slate-900 underline">Cerrar sesión</button>
        </form>
    </div>
</x-guest-layout>
