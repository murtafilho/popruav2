<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0d1117">

        <title>{{ config('app.name', 'POPRUA') }}</title>

        {{-- Google Fonts --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="guest-container">
            <div class="guest-logo">
                <a href="/">
                    <x-application-logo style="width: 64px; height: 64px; color: var(--accent-primary);" />
                </a>
            </div>

            <div class="card guest-card">
                <div class="card-body">
                    {{ $slot }}
                </div>
            </div>

            <p class="guest-footer">
                POPRUA v2 &copy; {{ date('Y') }} - Prefeitura de Belo Horizonte
            </p>
        </div>
    </body>
</html>
