<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>@yield('title', 'Mentea')</title>
    <style>
        :root { color-scheme: light dark; }
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body {
            margin: 0 !important; padding: 0 !important; width: 100% !important;
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #0f172a;
        }
        .wrapper { width: 100%; background-color: #f1f5f9; padding: 24px 12px; }
        .container {
            max-width: 560px; margin: 0 auto; background-color: #ffffff;
            border-radius: 16px; overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
            padding: 24px 28px; text-align: center;
        }
        .header-title { color: #ffffff; font-size: 20px; font-weight: 600; margin: 0; letter-spacing: 0.02em; }
        .header-sub { color: #ccfbf1; font-size: 12px; margin: 6px 0 0; }
        .body { padding: 28px 28px 16px; font-size: 15px; line-height: 1.55; color: #334155; }
        .body h1 { font-size: 18px; color: #0f172a; margin: 0 0 12px; }
        .body p { margin: 0 0 12px; }
        .meta {
            background-color: #f0fdfa; border: 1px solid #99f6e4; border-radius: 12px;
            padding: 14px 16px; margin: 16px 0; font-size: 14px; color: #0f766e;
        }
        .meta strong { color: #115e59; }
        .btn-wrap { text-align: center; margin: 24px 0 8px; }
        .btn {
            display: inline-block; background-color: #0d9488; color: #ffffff !important;
            text-decoration: none; padding: 12px 28px; border-radius: 9999px;
            font-weight: 600; font-size: 14px;
        }
        .footer {
            padding: 16px 28px 24px; font-size: 12px; color: #94a3b8; text-align: center;
            border-top: 1px solid #f1f5f9;
        }
        .footer a { color: #0d9488; text-decoration: none; }

        /* —— Oscuro (clientes que respetan prefers-color-scheme) —— */
        @media (prefers-color-scheme: dark) {
            body, .wrapper { background-color: #0f172a !important; }
            .container { background-color: #1e293b !important; border-color: #334155 !important; }
            .body { color: #cbd5e1 !important; }
            .body h1 { color: #f1f5f9 !important; }
            .meta { background-color: #134e4a !important; border-color: #0f766e !important; color: #99f6e4 !important; }
            .meta strong { color: #ccfbf1 !important; }
            .footer { color: #64748b !important; border-top-color: #334155 !important; }
            .btn { background-color: #14b8a6 !important; color: #042f2e !important; }
        }

        /* —— Móvil —— */
        @media only screen and (max-width: 620px) {
            .wrapper { padding: 12px 8px !important; }
            .body, .header, .footer { padding-left: 18px !important; padding-right: 18px !important; }
            .header-title { font-size: 18px !important; }
            .body { font-size: 15px !important; }
            .btn { display: block !important; width: 100% !important; box-sizing: border-box !important; text-align: center !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <p class="header-title">Mentea</p>
                <p class="header-sub">Cuidado de la salud mental</p>
            </div>
            <div class="body">
                @yield('content')
            </div>
            <div class="footer">
                <p>Este mensaje es informativo. No incluye datos clínicos sensibles.</p>
                <p><a href="{{ config('app.url') }}">{{ config('app.url') }}</a></p>
                <p style="margin-top:8px;">© {{ date('Y') }} Mentea</p>
            </div>
        </div>
    </div>
</body>
</html>
