<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mantenimiento — Mentea</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: Poppins, system-ui, sans-serif; margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, #042f2e 0%, #0f766e 45%, #134e4a 100%); color: #f0fdfa; }
        .card { max-width: 28rem; text-align: center; padding: 2.5rem 1.5rem; }
        .logo { height: 48px; margin: 0 auto 1.5rem; filter: brightness(0) invert(1); opacity: 0.95; }
        h1 { font-family: 'Playfair Display', Georgia, serif; font-size: 1.75rem; margin: 0 0 0.5rem; }
        .code { font-size: 3.5rem; font-weight: 600; color: rgba(255,255,255,0.25); line-height: 1; margin-bottom: 0.5rem; }
        p { color: #99f6e4; font-size: 0.95rem; margin: 0 0 0.5rem; }
        .hint { color: rgba(240,253,250,0.7); font-size: 0.8rem; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <div class="card">
        <img src="{{ asset('images/brand/logo-symbol.svg') }}" alt="Mentea" class="logo" onerror="this.style.display='none'">
        <div class="code" aria-hidden="true">503</div>
        <h1>Estamos en mantenimiento</h1>
        <p>Mentea no está disponible por un momento. Volveremos muy pronto.</p>
        <p class="hint">Si el problema persiste, escribe a soporte.</p>
    </div>
</body>
</html>
