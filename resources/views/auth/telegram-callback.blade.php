<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ config('app.name', 'OpenHabit') }}</title>
    <style>
        html, body { margin: 0; padding: 0; height: 100%; background: #fff; color: #171717; font-family: ui-sans-serif, system-ui, sans-serif; }
        @media (prefers-color-scheme: dark) { html, body { background: #171717; color: #fff; } }
        .wrap { min-height: 100%; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .msg { font-size: 14px; opacity: 0.7; }
    </style>
</head>
<body>
    <div class="wrap"><div class="msg">Signing you in…</div></div>
    <script>
        (function () {
            try {
                localStorage.setItem('api_token', @json($token));
            } catch (e) {
                // Storage may be unavailable (private mode); fall through to redirect.
            }
            window.location.replace(@json($redirectUrl));
        })();
    </script>
</body>
</html>
