<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Mentea') }} — Iniciar sesión</title>
        @include('partials.brand-head')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/mentea.css') }}">
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-mentea-soft">
            <div class="text-center px-4">
                <a href="/" class="inline-flex flex-col items-center gap-1 group">
                    <x-application-logo variant="full" class="h-28 sm:h-32 w-auto max-w-[280px]" />
                </a>
                <p class="mentea-tagline mt-1">Conecta · Exprésate · Cuida tu mente</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-soft overflow-hidden sm:rounded-2xl border border-slate-100">
                {{ $slot }}
            </div>

            <p class="mt-8 text-xs text-slate-400 max-w-sm text-center px-4">
                Gracias por priorizar tu salud mental.
            </p>
        </div>
    </body>
</html>
