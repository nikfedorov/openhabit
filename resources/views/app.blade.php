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
    <div id="app"></div>
</body>
</html>
