<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()?->preferred_theme === 'dark' ? 'dark' : '' }}" data-theme="{{ auth()->user()?->preferred_theme ?? 'system' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Mentea') }}@isset($title) — {{ $title }}@endisset</title>
        @include('partials.brand-head')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/mentea.css') }}">
    </head>
    <body class="font-sans antialiased">
    <a href="#main-content" class="mentea-skip-link">Saltar al contenido</a>
        <div class="mentea-page">
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-slate-200/60 bg-white/70 backdrop-blur-sm">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main id="main-content" class="pb-12">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-200/80 py-6 text-center text-xs text-slate-400">
                <span class="mentea-brand-wordmark text-sm" style="font-size:0.85rem;letter-spacing:0.06em;">MENTEA</span>
                <span class="mx-2">·</span>
                Conecta con tu bienestar mental · © {{ date('Y') }}
            </footer>
        </div>
    <script>
(function(){
  var t = document.documentElement.getAttribute('data-theme');
  if (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    document.documentElement.classList.add('dark');
  }
})();
</script>
    @include('partials.chat-widget')
</body>
</html>
