<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-base" content="{{ rtrim(url('/'), '/') }}">
    <meta name="theme-color" content="#0d1117">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="POPRUA">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">

    <title>{{ config('app.name', 'POPRUA') }} - @yield('title', 'Sistema')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    <div id="app">
        {{-- Sidebar Overlay --}}
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-logo">
                    <span class="sidebar-brand">POPRUA</span>
                    <span class="sidebar-version">v2.0</span>
                </a>
                <button type="button" class="sidebar-collapse-toggle" id="sidebar-collapse-toggle" title="Recolher menu">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <div class="sidebar-content">
                <nav class="sidebar-nav">
                    {{-- Operacional --}}
                    <div class="nav-section">
                        <span class="nav-section-title">Operacional</span>

                        <a href="{{ route('vistorias.index') }}" class="nav-item {{ request()->routeIs('vistorias.index') || (request()->routeIs('vistorias.*') && !request()->routeIs('vistorias.minhas')) ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <span class="nav-item-text">Vistorias</span>
                        </a>

                        <a href="{{ route('vistorias.minhas') }}" class="nav-item {{ request()->routeIs('vistorias.minhas') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="nav-item-text">Minhas Vistorias</span>
                        </a>

                        <a href="{{ route('pontos.nao-georreferenciados') }}" class="nav-item {{ request()->routeIs('pontos.nao-georreferenciados') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="nav-item-text">Pontos nao georreferenciados</span>
                        </a>

                        <a href="#" id="nav-sync-fotos" class="nav-item" onclick="event.preventDefault(); syncAllPendingPhotos();">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="nav-item-text">Sincronizar Imagens</span>
                            <span id="sync-badge" class="hidden" style="background: var(--color-warning); color: #000; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 10px; margin-left: auto;">0</span>
                        </a>
                    </div>

                    {{-- Menu Principal --}}
                    <div class="nav-section">
                        <span class="nav-section-title">Menu</span>

                        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="nav-item-text">Dashboard</span>
                        </a>

                        <a href="{{ route('mapa.index') }}" class="nav-item {{ request()->routeIs('mapa.*') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <span class="nav-item-text">Mapa</span>
                        </a>

                        <a href="{{ route('pontos.index') }}" class="nav-item {{ request()->routeIs('pontos.*') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="nav-item-text">Pontos</span>
                        </a>

                        <a href="{{ route('moradores.index') }}" class="nav-item {{ request()->routeIs('moradores.*') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="nav-item-text">Moradores</span>
                        </a>
                    </div>

                    {{-- Relatorios --}}
                    <div class="nav-section">
                        <span class="nav-section-title">Relatorios</span>

                        <a href="{{ route('powerbi.index') }}" class="nav-item {{ request()->routeIs('powerbi.*') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="nav-item-text">Power BI</span>
                        </a>

                        <a href="{{ route('discussao.index') }}" target="_blank" class="nav-item {{ request()->routeIs('discussao.*') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="nav-item-text">Discussao</span>
                        </a>
                    </div>

                    {{-- Administracao --}}
                    @can('ver usuarios')
                    <div class="nav-section">
                        <span class="nav-section-title">Administracao</span>

                        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="nav-item-text">Usuarios</span>
                        </a>

                        <a href="{{ route('admin.roles.index') }}" class="nav-item {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                            <svg class="nav-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="nav-item-text">Roles e Permissoes</span>
                        </a>
                    </div>
                    @endcan
                </nav>
            </div>

            <div class="sidebar-footer">
                @auth
                <div class="sidebar-user">
                    <div class="sidebar-user-avatar">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="sidebar-user-info">
                        <span class="sidebar-user-name">{{ Auth::user()->name }}</span>
                        <span class="sidebar-user-role">{{ Auth::user()->roles->first()?->name ?? 'Usuario' }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="sidebar-logout">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-icon" title="Sair">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="main-wrapper">
            {{-- Mobile Header --}}
            <header class="mobile-header">
                @hasSection('header')
                    @yield('header')
                @else
                    <a href="{{ route('dashboard') }}" class="mobile-header-back" aria-label="Voltar">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <span class="mobile-header-title">@yield('title', 'POPRUA')</span>
                    <div style="width: 44px;"></div>
                @endif
            </header>

            <main class="page has-mobile-header has-bottom-nav">
                @yield('content')
            </main>

        </div>

        {{-- Mobile Bottom Navigation --}}
        <nav class="bottom-nav" id="bottom-nav">
            <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="bottom-nav-label">Dashboard</span>
            </a>
            <a href="{{ route('mapa.index') }}" class="bottom-nav-item {{ request()->routeIs('mapa.*') ? 'active' : '' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span class="bottom-nav-label">Mapa</span>
            </a>
            <a href="{{ route('vistorias.minhas') }}" class="bottom-nav-item {{ request()->routeIs('vistorias.minhas') ? 'active' : '' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="bottom-nav-label">Minhas</span>
            </a>
            <a href="{{ route('moradores.index') }}" class="bottom-nav-item {{ request()->routeIs('moradores.*') ? 'active' : '' }}">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="bottom-nav-label">Moradores</span>
            </a>
            <button type="button" class="bottom-nav-item {{ request()->routeIs('admin.*') || request()->routeIs('powerbi.*') || request()->routeIs('pontos.*') || (request()->routeIs('vistorias.*') && !request()->routeIs('vistorias.minhas')) ? 'active' : '' }}" id="bottom-nav-more">
                <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="bottom-nav-label">Mais</span>
            </button>
        </nav>
    </div>

    @if(request()->routeIs('mapa.index') && request('geocoded') == '1' && request('ponto_id'))
    {{-- Painel de Confirmacao de Geocodificacao --}}
    <div id="geocode-panel" class="card card-glass" style="position: fixed; top: 70px; left: var(--space-4); right: var(--space-4); z-index: 99999; border: 2px solid var(--color-warning);">
        <div class="card-body">
            <div style="display: flex; gap: var(--space-3);">
                <div style="flex-shrink: 0;">
                    <svg style="width: 32px; height: 32px; color: var(--color-warning);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: var(--text-sm); font-weight: var(--font-semibold); color: var(--text-primary); margin: 0;">Confirmar Localização</h3>
                    <p class="text-muted" style="font-size: var(--text-sm); margin-top: var(--space-1);">
                        Clique no mapa para ajustar a posição ou confirme a localização atual.
                    </p>
                    <p id="geocode-coords" class="text-mono text-muted" style="margin-top: var(--space-2);"></p>
                </div>
            </div>
            <div style="display: flex; gap: var(--space-2); margin-top: var(--space-4);">
                <button id="btn-confirmar-geocode" class="btn btn-success" style="flex: 1;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Confirmar
                </button>
                <a href="{{ route('pontos.nao-georreferenciados') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Sidebar Toggle Script --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const collapseToggle = document.getElementById('sidebar-collapse-toggle');
        const bottomNavMore = document.getElementById('bottom-nav-more');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Bottom nav "Mais" button opens sidebar
        if (bottomNavMore) {
            bottomNavMore.addEventListener('click', function() {
                sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Desktop sidebar collapse toggle
        if (collapseToggle) {
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }
            collapseToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024 && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });
    });
    </script>

    {{-- Android back button exit confirmation --}}
    <script>
    (function() {
        // Push a dummy state so pressing back triggers popstate instead of exiting
        if (window.history.length <= 1) {
            window.history.pushState({ poprua: true }, '');
        }
        window.addEventListener('popstate', function(e) {
            // Re-push state to keep the guard active
            window.history.pushState({ poprua: true }, '');
            if (confirm('Deseja sair do aplicativo?')) {
                // Allow exit: go back twice (past our guard state)
                window.history.go(-2);
            }
        });
    })();
    </script>

    {{-- Sincronização global de fotos --}}
    <script>
    (function() {
        const APP_BASE = document.querySelector('meta[name="app-base"]').content;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function openFotosDB() {
            return new Promise((resolve, reject) => {
                const req = indexedDB.open('poprua_fotos', 1);
                req.onupgradeneeded = (e) => {
                    const db = e.target.result;
                    if (!db.objectStoreNames.contains('pendentes')) {
                        db.createObjectStore('pendentes', { keyPath: 'id', autoIncrement: true });
                    }
                };
                req.onsuccess = (e) => resolve(e.target.result);
                req.onerror = (e) => reject(e.target.error);
            });
        }

        async function countPendingPhotos() {
            try {
                const db = await openFotosDB();
                return new Promise((resolve) => {
                    const tx = db.transaction('pendentes', 'readonly');
                    const req = tx.objectStore('pendentes').count();
                    req.onsuccess = () => resolve(req.result);
                    req.onerror = () => resolve(0);
                });
            } catch { return 0; }
        }

        async function updateSyncBadge() {
            const count = await countPendingPhotos();
            const badge = document.getElementById('sync-badge');
            if (badge) {
                badge.textContent = count;
                badge.classList.toggle('hidden', count === 0);
            }
        }

        window.syncAllPendingPhotos = async function() {
            const db = await openFotosDB();
            const tx = db.transaction('pendentes', 'readonly');
            const req = tx.objectStore('pendentes').getAll();

            req.onsuccess = async () => {
                const fotos = req.result;
                if (fotos.length === 0) {
                    alert('Nenhuma foto pendente para sincronizar.');
                    return;
                }

                if (!confirm(`Enviar ${fotos.length} foto(s) pendente(s)?`)) return;

                let enviadas = 0;
                let erros = 0;

                for (const foto of fotos) {
                    try {
                        const blob = new Blob([foto.data], { type: foto.type });
                        const file = new File([blob], foto.name, { type: foto.type });
                        const formData = new FormData();
                        formData.append('vistoria_id', foto.vistoria_id);
                        formData.append('foto', file);

                        const resp = await fetch(`${APP_BASE}/api/vistorias/fotos`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken },
                            body: formData
                        });

                        if (resp.ok) {
                            const delTx = db.transaction('pendentes', 'readwrite');
                            delTx.objectStore('pendentes').delete(foto.id);
                            await new Promise(r => { delTx.oncomplete = r; });
                            enviadas++;
                        } else {
                            erros++;
                        }
                    } catch {
                        erros++;
                    }
                }

                await updateSyncBadge();
                alert(`${enviadas} foto(s) enviada(s)` + (erros > 0 ? `, ${erros} erro(s)` : ''));
            };
        };

        // Atualiza badge ao carregar
        updateSyncBadge();
    })();
    </script>

    @stack('scripts')

    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('{{ asset("sw.js") }}', { scope: '{{ asset("/") }}' });
    }
    </script>
</body>
</html>
