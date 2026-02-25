@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3" style="color: var(--text-primary);">
        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="mobile-header-title">{{ config('app.name') }}</span>
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('profile.edit') }}" class="btn btn-ghost btn-icon">
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="btn btn-ghost btn-icon" onclick="return confirm('{{ __('Deseja sair?') }}')">
                <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>
@endsection

@section('content')
    <div class="page-content">
        <div class="container">
            <div class="grid grid-cols-2 grid-mobile-1 gap-3">

                {{-- Mapa --}}
                <a href="{{ route('mapa.index') }}" class="card card-interactive dashboard-card animate-slide-up stagger-1">
                    <div class="dashboard-card-icon" style="background: var(--color-info);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <span class="dashboard-card-title">{{ __('Mapa') }}</span>
                </a>

                {{-- Nova Vistoria --}}
                <a href="{{ route('mapa.index', ['nova_vistoria' => '1']) }}" class="card card-interactive dashboard-card animate-slide-up stagger-2">
                    <div class="dashboard-card-icon" style="background: var(--color-success);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="dashboard-card-title">{{ __('Nova Vistoria') }}</span>
                </a>

                {{-- Pontos --}}
                <a href="{{ route('pontos.index') }}" class="card card-interactive dashboard-card animate-slide-up stagger-3">
                    <div class="dashboard-card-icon" style="background: #6366f1;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span class="dashboard-card-title">Pontos</span>
                </a>

                {{-- Pontos Não Georreferenciados --}}
                <a href="{{ route('pontos.nao-georreferenciados') }}" class="card card-interactive dashboard-card animate-slide-up stagger-4">
                    <div class="dashboard-card-icon" style="background: var(--color-warning);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="dashboard-card-title">Sem Coordenadas</span>
                </a>

                {{-- Vistorias --}}
                <a href="{{ route('vistorias.index') }}" class="card card-interactive dashboard-card animate-slide-up stagger-5">
                    <div class="dashboard-card-icon" style="background: var(--accent-primary);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="dashboard-card-title">Vistorias</span>
                </a>

                {{-- Moradores --}}
                <a href="{{ route('moradores.index') }}" class="card card-interactive dashboard-card animate-slide-up stagger-6">
                    <div class="dashboard-card-icon" style="background: #f43f5e;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="dashboard-card-title">Moradores</span>
                </a>

                {{-- Power BI --}}
                @can('ver relatorios')
                <a href="{{ route('powerbi.index') }}" class="card card-interactive dashboard-card animate-slide-up">
                    <div class="dashboard-card-icon" style="background: #f59e0b;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="dashboard-card-title">Power BI</span>
                </a>
                @endcan

                {{-- Admin Roles --}}
                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.roles.index') }}" class="card card-interactive dashboard-card animate-slide-up">
                    <div class="dashboard-card-icon" style="background: #8b5cf6;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <span class="dashboard-card-title">{{ __('Admin') }}</span>
                </a>
                @endif

                {{-- Perfil --}}
                <a href="{{ route('profile.edit') }}" class="card card-interactive dashboard-card animate-slide-up">
                    <div class="dashboard-card-icon" style="background: var(--bg-elevated);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="dashboard-card-title">{{ __('Meu Perfil') }}</span>
                </a>

            </div>
        </div>
    </div>
@endsection
