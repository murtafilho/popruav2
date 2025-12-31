<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="html-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e2939">

    <title>{{ config('app.name', 'POPRUA') }} - @yield('title', 'Sistema')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    
    <script>
        // Aplicar dark mode antes do carregamento para evitar flash
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
        
        // Garantir que toggleDarkMode está disponível antes do Vite carregar
        window.toggleDarkMode = function() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            
            // Atualizar ícones
            const darkIcons = document.querySelectorAll('[data-dark-icon]');
            const lightIcons = document.querySelectorAll('[data-light-icon]');
            
            const nowDark = html.classList.contains('dark');
            darkIcons.forEach(icon => {
                icon.classList.toggle('hidden', !nowDark);
            });
            lightIcons.forEach(icon => {
                icon.classList.toggle('hidden', nowDark);
            });
        };
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 antialiased transition-colors duration-200">
    <div id="app" class="h-full flex flex-col">
        @hasSection('header')
            <header class="bg-[#1e2939] text-white px-4 py-3 flex items-center justify-between shadow-md z-10 transition-colors duration-200">
                @yield('header')
            </header>
        @endif

        <main class="flex-1 relative overflow-hidden">
            @yield('content')
        </main>

        @hasSection('footer')
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 py-2 z-10 transition-colors duration-200">
                @yield('footer')
            </footer>
        @endif
    </div>

    @stack('scripts')
</body>
</html>
