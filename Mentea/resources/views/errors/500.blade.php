<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error — Mentea</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: Poppins, system-ui, sans-serif; margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: #f8fafc; color: #0f172a; }
        .card { max-width: 28rem; text-align: center; padding: 2.5rem 1.5rem; }
        h1 { font-family: 'Playfair Display', Georgia, serif; font-size: 1.5rem; color: #0f766e; }
        p { color: #64748b; font-size: 0.95rem; }
        a { color: #0d9488; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Algo salió mal</h1>
        <p>Ocurrió un error inesperado. Puedes volver al <a href="{{ url('/') }}">inicio</a> o intentar de nuevo en unos minutos.</p>
    </div>
</body>
</html>
