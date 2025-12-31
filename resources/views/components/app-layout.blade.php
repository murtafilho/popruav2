<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#1e2939">

        <title>{{ config('app.name', 'POPRUA') }} - @isset($title){{ $title }}@else{{ 'Sistema' }}@endisset</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="bg-gray-100 antialiased">
        <div id="app" class="min-h-screen flex flex-col">
            <!-- Page Header (opcional, definido por cada view) -->
            @isset($header)
                <header class="bg-[#1e2939] text-white px-4 py-3 flex items-center justify-between shadow-md z-10 sticky top-0">
                    {{ $header }}
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 relative overflow-hidden">
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
