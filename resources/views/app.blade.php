<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>{{ config('app.name', 'OpenHabit') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="manifest" href="/site.webmanifest">
    {{-- Required for Telegram Mini App context detection (window.Telegram.WebApp). --}}
    {{-- Safe to include on all pages: outside Telegram initData is an empty string. --}}
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            var isDark = theme === 'dark' || (theme !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) document.documentElement.classList.add('dark');

            var bgColor = isDark ? '#171717' : '#ffffff';
            document.documentElement.style.backgroundColor = bgColor;

            var tg = window.Telegram && window.Telegram.WebApp;
            if (tg && tg.isVersionAtLeast && tg.isVersionAtLeast('6.1')) {
                tg.setHeaderColor && tg.setHeaderColor(bgColor);
                tg.setBackgroundColor && tg.setBackgroundColor(bgColor);
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @if($devToken ?? false)
    <script>localStorage.setItem('api_token', '{{ $devToken }}');</script>
    @endif
</head>
<body class="antialiased">
    <div id="app">
        <div id="splash" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding-bottom:20vh;background:#fff">
            <svg id="splash-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="none" width="64" height="64"><rect id="splash-bg" width="512" height="512" rx="96" fill="#f5f5f5"/><rect x="80" y="272" width="160" height="160" rx="24" fill="#14532d"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0s" repeatCount="indefinite"/></rect><rect x="272" y="272" width="160" height="160" rx="24" fill="#15803d"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.2s" repeatCount="indefinite"/></rect><rect x="80" y="80" width="160" height="160" rx="24" fill="#16a34a"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.4s" repeatCount="indefinite"/></rect><rect x="272" y="80" width="160" height="160" rx="24" fill="#22c55e"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.6s" repeatCount="indefinite"/></rect></svg>
        </div>
        <style>.dark #splash{background:#171717!important}.dark #splash-bg{fill:#171717}</style>
    </div>
    @if($trackingScripts = \App\Models\Setting::trackingScripts())
        {!! $trackingScripts !!}
    @endif
</body>
</html>
