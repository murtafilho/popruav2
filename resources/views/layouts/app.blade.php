<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#3b82f6">

    <title>{{ config('app.name', 'POPRUA') }} - @yield('title', 'Sistema')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-gray-100 antialiased">
    <div id="app" class="h-full flex flex-col">
        @hasSection('header')
            <header class="bg-blue-500 text-white px-4 py-3 flex items-center justify-between shadow-md z-10">
                @yield('header')
            </header>
        @endif

        <main class="flex-1 relative overflow-hidden">
            @yield('content')
        </main>

        @hasSection('footer')
            <footer class="bg-white border-t border-gray-200 px-4 py-2 z-10">
                @yield('footer')
            </footer>
        @endif
    </div>

    @stack('scripts')
</body>
</html>
