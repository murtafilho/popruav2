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
            const theme = localStorage.getItem('theme') || 'dark';
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
            <header class="bg-blue-600 text-white px-4 py-3 flex items-center justify-between shadow-md z-10 transition-colors duration-200">
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

    @if(request()->routeIs('mapa.index') && request('geocoded') == '1' && request('ponto_id'))
    <!-- Painel de Confirmação de Geocodificação -->
    <div id="geocode-panel" style="position: fixed; top: 70px; left: 16px; right: 16px; z-index: 99999; background: white; padding: 16px; border: 3px solid #eab308; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <div style="display: flex; align-items: flex-start; gap: 12px;">
            <div style="flex-shrink: 0;">
                <svg style="width: 32px; height: 32px; color: #eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 14px; font-weight: 600; color: #111; margin: 0;">Confirmar Localização</h3>
                <p style="font-size: 12px; color: #666; margin-top: 4px;">
                    Clique no mapa para ajustar a posição ou confirme a localização atual.
                </p>
                <p id="geocode-coords" style="font-size: 12px; color: #888; margin-top: 8px; font-family: monospace;"></p>
            </div>
        </div>
        <div style="display: flex; gap: 8px; margin-top: 16px;">
            <button
                id="btn-confirmar-geocode"
                style="flex: 1; padding: 10px 16px; background: #22c55e; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;"
            >
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Confirmar
            </button>
            <a
                href="{{ route('pontos.nao-georreferenciados') }}"
                style="padding: 10px 16px; background: #e5e7eb; color: #374151; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 500;"
            >
                Cancelar
            </a>
        </div>
    </div>
    @endif

    @stack('scripts')
</body>
</html>
