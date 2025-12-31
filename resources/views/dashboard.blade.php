@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-80 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <h1 class="text-lg font-semibold">POPRUA v2</h1>
        </a>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('profile.edit') }}" class="p-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="p-2 rounded-lg hover:bg-white/10 transition" onclick="return confirm('{{ __('Deseja sair?') }}')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 p-4">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <!-- Mapa -->
                <a href="{{ route('mapa.index') }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-100 p-2.5 rounded-lg">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ __('Mapa') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('Visualizar pontos e vistorias') }}</p>
                        </div>
                    </div>
                </a>

                <!-- Nova Vistoria -->
                <a href="{{ route('vistorias.create') }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 p-2.5 rounded-lg">
                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ __('Nova Vistoria') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('Registrar nova vistoria') }}</p>
                        </div>
                    </div>
                </a>

                <!-- Pontos -->
                <a href="{{ route('pontos.index') }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-indigo-100 p-2.5 rounded-lg">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Pontos</h3>
                            <p class="text-xs text-gray-500">Lista tabular de pontos</p>
                        </div>
                    </div>
                </a>

                <!-- Vistorias -->
                <a href="{{ route('vistorias.index') }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-teal-100 p-2.5 rounded-lg">
                            <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Vistorias</h3>
                            <p class="text-xs text-gray-500">Lista tabular de vistorias</p>
                        </div>
                    </div>
                </a>

                <!-- Admin Roles -->
                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.roles.index') }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-purple-100 p-2.5 rounded-lg">
                            <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ __('Administração') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('Gerenciar roles e permissões') }}</p>
                        </div>
                    </div>
                </a>
                @endif

                <!-- Perfil -->
                <a href="{{ route('profile.edit') }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="bg-gray-100 p-2.5 rounded-lg">
                            <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ __('Meu Perfil') }}</h3>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </div>
@endsection
