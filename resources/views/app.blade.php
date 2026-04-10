<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'OpenHabit') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @if($devToken ?? false)
    <script>localStorage.setItem('api_token', '{{ $devToken }}');</script>
    @endif
</head>
<body>
    <div id="app">
        <div id="splash" style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#fff">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="none" width="64" height="64"><rect width="512" height="512" rx="96" fill="#171717"/><rect x="80" y="272" width="160" height="160" rx="24" fill="#14532d"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0s" repeatCount="indefinite"/></rect><rect x="272" y="272" width="160" height="160" rx="24" fill="#15803d"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.2s" repeatCount="indefinite"/></rect><rect x="80" y="80" width="160" height="160" rx="24" fill="#16a34a"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.4s" repeatCount="indefinite"/></rect><rect x="272" y="80" width="160" height="160" rx="24" fill="#22c55e"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.6s" repeatCount="indefinite"/></rect></svg>
        </div>
        <style>@media(prefers-color-scheme:dark){#splash{background:#171717!important}}</style>
    </div>
</body>
</html>
