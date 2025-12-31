@extends('layouts.app')

@section('title', 'Detalhes do Ponto')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('pontos.index') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-semibold flex-1 text-center">Detalhes do Ponto</h1>
        <div class="w-10"></div>
    </div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 transition-colors duration-200">
        <!-- Informações do Ponto (Mestre) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-4 mb-4 transition-colors duration-200">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações do Ponto</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-200 dark:text-gray-300 mb-1">Endereço</label>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        @if($ponto->tipo)
                            {{ $ponto->tipo }} 
                        @endif
                        {{ $ponto->logradouro }}, {{ $ponto->numero }}
                        @if($ponto->complemento)
                            <span class="text-gray-500 dark:text-gray-400">- {{ $ponto->complemento }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-200 dark:text-gray-300 mb-1">Bairro / Regional</label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $ponto->bairro }} - {{ $ponto->regional }}</p>
                </div>
                @if($ponto->lat && $ponto->lng)
                <div>
                    <label class="block text-xs font-medium text-gray-200 dark:text-gray-300 mb-1">Coordenadas</label>
                    <a href="{{ route('mapa.index', ['lat' => $ponto->lat, 'lng' => $ponto->lng, 'zoom' => 19]) }}" 
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ number_format($ponto->lat, 6) }}, {{ number_format($ponto->lng, 6) }}
                    </a>
                </div>
                @endif
                <div>
                    <label class="block text-xs font-medium text-gray-200 dark:text-gray-300 mb-1">Total de Vistorias</label>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                        {{ $ponto->total_vistorias }}
                    </span>
                </div>
                @if($ponto->resultado_acao)
                <div>
                    <label class="block text-xs font-medium text-gray-200 dark:text-gray-300 mb-1">Último Resultado</label>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        {{ $ponto->resultado_acao_id == 1 ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                        {{ $ponto->resultado_acao_id == 2 ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                        {{ $ponto->resultado_acao_id == 3 ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                        {{ $ponto->resultado_acao_id == 4 ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                        {{ $ponto->resultado_acao_id == 5 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                        {{ $ponto->resultado_acao_id == 6 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                    ">
                        {{ $ponto->resultado_acao }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Lista de Vistorias (Detalhe) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 overflow-hidden transition-colors duration-200">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Vistorias ({{ $vistorias->count() }})</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ordenadas por data decrescente</p>
            </div>

            @if($vistorias->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase">Data/Hora</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase hidden sm:table-cell">Tipo Abordagem</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase">Pessoas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase hidden md:table-cell">Kg</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase">Resultado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-200 dark:text-gray-200 uppercase hidden lg:table-cell">Usuário</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($vistorias as $vistoria)
                                <tr class="odd:bg-gray-50 dark:odd:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($vistoria->data_abordagem)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($vistoria->data_abordagem)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-200 dark:text-gray-300 hidden sm:table-cell">
                                        {{ $vistoria->tipo_abordagem ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-200 dark:text-gray-300">
                                        @if($vistoria->quantidade_pessoas)
                                            <div class="font-medium">{{ $vistoria->quantidade_pessoas }}</div>
                                            @if($vistoria->nomes_pessoas)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ \Illuminate\Support\Str::limit($vistoria->nomes_pessoas, 30) }}</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-200 dark:text-gray-300 hidden md:table-cell">
                                        @if($vistoria->qtd_kg)
                                            {{ $vistoria->qtd_kg }} kg
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($vistoria->resultado_acao)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ str_contains($vistoria->resultado_acao, 'persiste') ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                                {{ str_contains($vistoria->resultado_acao, 'parcialmente') ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                                                {{ str_contains($vistoria->resultado_acao, 'Extinto') ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                                {{ str_contains($vistoria->resultado_acao, 'ausente') ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                                {{ str_contains($vistoria->resultado_acao, 'constatado') ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                                {{ str_contains($vistoria->resultado_acao, 'Conformidade') ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                            ">
                                                {{ $vistoria->resultado_acao }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-200 dark:text-gray-300 hidden lg:table-cell">
                                        {{ $vistoria->usuario ?? '-' }}
                                    </td>
                                </tr>
                                @if($vistoria->observacao)
                                <tr class="bg-gray-50 dark:bg-gray-700/30">
                                    <td colspan="6" class="px-4 py-2">
                                        <div class="text-xs text-gray-600 dark:text-gray-300">
                                            <strong>Observação:</strong> {{ $vistoria->observacao }}
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    Nenhuma vistoria registrada para este ponto.
                </div>
            @endif
        </div>
    </div>
@endsection
