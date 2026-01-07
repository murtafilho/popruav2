@extends('layouts.app')

@section('title', 'Pontos')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-semibold flex-1 text-center">{{ __('Pontos') }}</h1>
        <div class="w-10"></div>
    </div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 transition-colors duration-200">
        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-4 mb-4 transition-colors duration-200">
            <form method="GET" action="{{ route('pontos.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-200 dark:text-gray-300 mb-1">Bairro</label>
                        <select name="bairro" class="w-full px-4 py-3 text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg bg-[#93a6c2] text-black focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">Todos</option>
                            @foreach($bairros as $bairro)
                                <option value="{{ $bairro }}" {{ request('bairro') == $bairro ? 'selected' : '' }}>
                                    {{ $bairro }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-200 dark:text-gray-300 mb-1">Regional</label>
                        <select name="regional" class="w-full px-4 py-3 text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg bg-[#93a6c2] text-black focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">Todas</option>
                            @foreach($regionais as $regional)
                                <option value="{{ $regional }}" {{ request('regional') == $regional ? 'selected' : '' }}>
                                    {{ $regional }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-200 dark:text-gray-300 mb-1">Resultado</label>
                        <select name="resultado" class="w-full px-4 py-3 text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg bg-[#93a6c2] text-black focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">Todos</option>
                            @foreach($resultados as $resultado)
                                <option value="{{ $resultado->id }}" {{ request('resultado') == $resultado->id ? 'selected' : '' }}>
                                    {{ $resultado->resultado }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                        Filtrar
                    </button>
                    <a href="{{ route('pontos.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-200 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabela -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 overflow-hidden transition-colors duration-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase">Endereço</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase hidden sm:table-cell">Bairro</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase hidden md:table-cell">Regional</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase hidden lg:table-cell">Vistorias</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase">Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($pontos as $ponto)
                            <tr class="odd:bg-gray-50 dark:odd:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        <a href="{{ route('mapa.index', ['lat' => $ponto->lat, 'lng' => $ponto->lng, 'zoom' => 19]) }}" 
                                           class="hover:text-blue-600 hover:underline transition flex items-center gap-1">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $ponto->tipo }} {{ $ponto->logradouro }}, {{ $ponto->numero }}
                                            @if($ponto->complemento)
                                                <span class="text-gray-500 dark:text-gray-400">- {{ $ponto->complemento }}</span>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 sm:hidden mt-1">
                                        {{ $ponto->bairro }} - {{ $ponto->regional }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 lg:hidden mt-1">
                                        @if($ponto->total_vistorias > 0)
                                            <a href="{{ route('pontos.show', $ponto->id) }}" class="inline-flex items-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                    {{ $ponto->total_vistorias }} vistoria(s)
                                                </span>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                {{ $ponto->total_vistorias }} vistoria(s)
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-200 dark:text-gray-300 hidden sm:table-cell">{{ $ponto->bairro }}</td>
                                <td class="px-4 py-3 text-gray-200 dark:text-gray-300 hidden md:table-cell">{{ $ponto->regional }}</td>
                                <td class="px-4 py-3 text-gray-200 dark:text-gray-300 hidden lg:table-cell">
                                    @if($ponto->total_vistorias > 0)
                                        <a href="{{ route('pontos.show', $ponto->id) }}" class="inline-flex items-center hover:opacity-80 transition">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ $ponto->total_vistorias }}
                                            </span>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            {{ $ponto->total_vistorias }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($ponto->resultado_acao)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $ponto->resultado_acao_id == 1 ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $ponto->resultado_acao_id == 2 ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $ponto->resultado_acao_id == 3 ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $ponto->resultado_acao_id == 4 ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $ponto->resultado_acao_id == 5 ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $ponto->resultado_acao_id == 6 ? 'bg-green-100 text-green-800' : '' }}
                                            {{ !$ponto->resultado_acao_id ? 'bg-purple-100 text-purple-800' : '' }}
                                        ">
                                            {{ $ponto->resultado_acao }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Sem vistoria
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Nenhum ponto encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($pontos->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $pontos->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
