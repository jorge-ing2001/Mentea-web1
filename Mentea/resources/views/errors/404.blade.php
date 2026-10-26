<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página no encontrada — Mentea</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mentea.css') }}">
    <style>
        body { font-family: Poppins, system-ui, sans-serif; margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, #f0fdfa 0%, #f8fafc 50%, #ecfdf5 100%); color: #0f172a; }
        .card { max-width: 28rem; text-align: center; padding: 2.5rem 1.5rem; }
        .logo { height: 48px; margin: 0 auto 1.5rem; }
        h1 { font-family: 'Playfair Display', Georgia, serif; font-size: 1.75rem; margin: 0 0 0.5rem; color: #0f766e; }
        .code { font-size: 4rem; font-weight: 600; color: #99f6e4; line-height: 1; margin-bottom: 0.5rem; }
        p { color: #64748b; font-size: 0.95rem; margin: 0 0 1.5rem; }
        a.btn { display: inline-block; background: linear-gradient(135deg, #14b8a6, #0f766e); color: #fff; text-decoration: none;
            padding: 0.65rem 1.25rem; border-radius: 9999px; font-weight: 500; font-size: 0.9rem; }
        a.btn:hover { filter: brightness(1.05); }
        a.sec { display: block; margin-top: 1rem; color: #0d9488; font-size: 0.85rem; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ asset('images/brand/logo-symbol.svg') }}" alt="Mentea" class="logo" onerror="this.style.display='none'">
        <div class="code" aria-hidden="true">404</div>
        <h1>Página no encontrada</h1>
        <p>No encontramos lo que buscas. Puede que el enlace esté roto o la página ya no exista.</p>
        <a class="btn" href="{{ url('/') }}">Ir al inicio</a>
        @auth
            <a class="sec" href="{{ url('/dashboard') }}">Ir a mi panel</a>
        @endauth
    </div>
</body>
</html>
