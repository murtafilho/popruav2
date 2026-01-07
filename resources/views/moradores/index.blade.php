@extends('layouts.app')

@section('title', 'Moradores')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-semibold flex-1 text-center">Moradores</h1>
        <a href="{{ route('moradores.create') }}" class="p-2 rounded-lg hover:bg-white/10 transition" title="Novo morador">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </a>
    </div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 transition-colors duration-200">
        <!-- Mensagens -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-4 mb-4 transition-colors duration-200">
            <form method="GET" action="{{ route('moradores.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome ou apelido..."
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Genero</label>
                        <select name="genero" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">Todos</option>
                            @foreach($generos as $genero)
                                <option value="{{ $genero }}" {{ request('genero') == $genero ? 'selected' : '' }}>
                                    {{ $genero }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Situacao</label>
                        <select name="situacao" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">Todos</option>
                            <option value="com_ponto" {{ request('situacao') == 'com_ponto' ? 'selected' : '' }}>Com ponto</option>
                            <option value="sem_ponto" {{ request('situacao') == 'sem_ponto' ? 'selected' : '' }}>Sem ponto</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                        Filtrar
                    </button>
                    <a href="{{ route('moradores.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Lista -->
        <div class="space-y-3">
            @forelse($moradores as $morador)
                <a href="{{ route('moradores.show', $morador) }}"
                   class="block bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-4 hover:shadow-md transition-all duration-200">
                    <div class="flex items-start gap-4">
                        <!-- Foto -->
                        <div class="flex-shrink-0">
                            @if($morador->fotografia)
                                <img src="{{ Storage::url($morador->fotografia) }}"
                                     alt="{{ $morador->nome_social }}"
                                     class="w-14 h-14 rounded-full object-cover">
                            @else
                                <div class="w-14 h-14 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                                    {{ $morador->nome_social }}
                                </h3>
                                @if($morador->apelido)
                                    <span class="text-sm text-gray-500 dark:text-gray-400">({{ $morador->apelido }})</span>
                                @endif
                            </div>

                            @if($morador->genero)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $morador->genero }}</p>
                            @endif

                            @if($morador->pontoAtual)
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    {{ $morador->pontoAtual->endereco->logradouro ?? '' }}, {{ $morador->pontoAtual->numero }}
                                </p>
                            @else
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Sem ponto vinculado</p>
                            @endif
                        </div>

                        <!-- Seta -->
                        <div class="flex-shrink-0 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Nenhum morador encontrado.</p>
                    <a href="{{ route('moradores.create') }}" class="inline-block mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                        Cadastrar primeiro morador
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Paginacao -->
        @if($moradores->hasPages())
            <div class="mt-4">
                {{ $moradores->links() }}
            </div>
        @endif
    </div>
@endsection
