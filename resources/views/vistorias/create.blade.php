@extends('layouts.app')

@section('title', 'Nova Vistoria')

@section('header')
    <a href="{{ route('mapa.index') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h1 class="text-lg font-semibold flex-1 text-center">Nova Vistoria</h1>
    <div class="w-10"></div>
@endsection

@section('content')
    <div class="h-full flex flex-col bg-gray-50 dark:bg-gray-900">
        <form id="vistoria-form" action="{{ route('vistorias.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col h-full">
            @csrf
            <input type="hidden" name="lat" value="{{ $lat }}">
            <input type="hidden" name="lng" value="{{ $lng }}">
            @if($pontoProximo)
                <input type="hidden" name="ponto_id" value="{{ $pontoProximo->id }}">
            @endif

            <!-- Navegação das Abas -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
                <div class="flex">
                    <button type="button" onclick="showTab(0)" class="tab-btn flex-1 py-3 px-2 text-xs font-medium text-center border-b-2 transition-colors" data-tab="0">
                        <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Dados
                    </button>
                    <button type="button" onclick="showTab(1)" class="tab-btn flex-1 py-3 px-2 text-xs font-medium text-center border-b-2 transition-colors" data-tab="1">
                        <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Perfil
                    </button>
                    <button type="button" onclick="showTab(2)" class="tab-btn flex-1 py-3 px-2 text-xs font-medium text-center border-b-2 transition-colors" data-tab="2">
                        <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Fiscalização
                    </button>
                    <button type="button" onclick="showTab(3)" class="tab-btn flex-1 py-3 px-2 text-xs font-medium text-center border-b-2 transition-colors" data-tab="3">
                        <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Moradores
                    </button>
                    <button type="button" onclick="showTab(4)" class="tab-btn flex-1 py-3 px-2 text-xs font-medium text-center border-b-2 transition-colors" data-tab="4">
                        <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Fotos
                    </button>
                </div>
            </div>

            <!-- Conteúdo das Abas -->
            <div class="flex-1 overflow-y-auto">
                <!-- Aba 1: Dados Básicos -->
                <div class="tab-content p-4 space-y-4" data-tab="0">
                    <!-- Localização -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Localização</h3>
                        @if($pontoProximo)
                            <p class="text-sm text-green-600">
                                <span class="font-medium">Ponto existente:</span>
                                {{ $pontoProximo->endereco->logradouro ?? '' }}, {{ $pontoProximo->numero }} - {{ $pontoProximo->endereco->bairro ?? '' }}
                            </p>
                        @else
                            <p class="text-sm text-orange-600">
                                Novo ponto será criado
                            </p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">
                            Lat: {{ number_format($lat, 6) }} | Lng: {{ number_format($lng, 6) }}
                        </p>
                    </div>

                    <!-- Dados da Vistoria -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm space-y-3">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">Dados da Vistoria</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data/Hora da Abordagem *</label>
                            <input type="datetime-local" name="data_abordagem" value="{{ date('Y-m-d\TH:i') }}" required
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Abordagem *</label>
                            <select name="tipo_abordagem_id" required
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecione...</option>
                                @foreach($tiposAbordagem as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Resultado da Ação *</label>
                            <select name="resultado_acao_id" required
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecione...</option>
                                @foreach($resultadosAcao as $resultado)
                                    <option value="{{ $resultado->id }}">{{ $resultado->resultado }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Qtd. Pessoas</label>
                                <input type="number" name="quantidade_pessoas" min="0" value="0"
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Qtd. Kg</label>
                                <input type="number" name="qtd_kg" min="0" value="0"
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba 2: Perfil da Ocorrência -->
                <div class="tab-content p-4 space-y-4 hidden" data-tab="1">
                    <!-- Nomes das Pessoas -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomes das Pessoas</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Um nome por linha</p>
                        <div class="relative">
                            <textarea name="nomes_pessoas" id="nomes_pessoas" rows="3" placeholder="Digite um nome por linha..."
                                      class="w-full px-4 py-3 pr-12 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400 resize-none"></textarea>
                            <button type="button" onclick="startVoiceInput('nomes_pessoas')"
                                    class="voice-btn absolute right-2 top-3 p-2 text-gray-500 hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Abrigos -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm space-y-3">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">Abrigos</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Qtd. Abrigos Provisórios</label>
                            <input type="number" name="qtd_abrigos_provisorios" id="qtd_abrigos" min="0" value="0"
                                   onchange="atualizarCamposAbrigos()"
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div id="abrigos-container" class="space-y-2 hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipos de Abrigo Desmontado</label>
                            <div id="abrigos-list" class="space-y-2"></div>
                        </div>

                        <div id="tipo-abrigo-unico">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Abrigo Desmontado</label>
                            <select name="tipo_abrigo_desmontado_id"
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Nenhum</option>
                                @foreach($tiposAbrigo as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->tipo_abrigo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Fatores de Complexidade -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Fatores de Complexidade</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="resistencia" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Resistência</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="num_reduzido" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Núm. Reduzido</span>
                            </label>

                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="casal" id="checkbox_casal" value="1" onchange="toggleQtdCasais()" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Casal</span>
                            </label>
                            <div id="qtd_casais_container" class="hidden">
                                <input type="number" name="qtd_casais" id="qtd_casais" min="1" value="1" placeholder="Qtd."
                                       class="w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="catador_reciclados" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Catador</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="fixacao_antiga" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Fixação Antiga</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="excesso_objetos" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Excesso Objetos</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="trafico_ilicitos" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Tráfico/Ilícitos</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="crianca_adolescente" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Criança/Adolesc.</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="idosos" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Idosos</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="gestante" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Gestante</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="lgbtqiapn" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">LGBTQIAPN+</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="deficiente" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Deficiente</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="agrupamento_quimico" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Agrup. Químico</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="saude_mental" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Saúde Mental</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="cena_uso_caracterizada" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Cena de Uso</span>
                            </label>

                            <label class="flex items-center gap-2 text-sm p-2 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="animais" id="checkbox_animais" value="1" onchange="toggleQtdAnimais()" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Animais</span>
                            </label>
                            <div id="qtd_animais_container" class="hidden">
                                <input type="number" name="qtd_animais" id="qtd_animais" min="1" value="1" placeholder="Qtd."
                                       class="w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba 3: Fiscalização -->
                <div class="tab-content p-4 space-y-4 hidden" data-tab="2">
                    <!-- Condução e Fiscalização -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm space-y-4">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">Ações de Fiscalização</h3>

                        <!-- Condução pelas Forças de Segurança -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Condução pelas Forças de Segurança</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="conducao_forcas_seguranca" value="1" onchange="toggleConducaoObs()" class="text-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Sim</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="conducao_forcas_seguranca" value="0" checked onchange="toggleConducaoObs()" class="text-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Não</span>
                                </label>
                            </div>
                            <div id="conducao_obs_container" class="mt-2 hidden">
                                <textarea name="conducao_forcas_observacao" id="conducao_forcas_observacao" rows="2" placeholder="Observação sobre a condução..."
                                          class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Apreensão Fiscal -->
                        <div>
                            <label class="flex items-center gap-2 text-sm p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <input type="checkbox" name="apreensao_fiscal" value="1" class="rounded text-blue-500 w-5 h-5">
                                <span class="text-gray-700 dark:text-gray-300">Apreensão Fiscal</span>
                            </label>
                        </div>

                        <!-- Auto de Fiscalização Aplicado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Auto de Fiscalização Aplicado</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="auto_fiscalizacao_aplicado" value="1" onchange="toggleAutoNumero()" class="text-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Sim</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="auto_fiscalizacao_aplicado" value="0" checked onchange="toggleAutoNumero()" class="text-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Não</span>
                                </label>
                            </div>
                            <div id="auto_numero_container" class="mt-2 hidden">
                                <input type="text" name="auto_fiscalizacao_numero" id="auto_fiscalizacao_numero" placeholder="Número do Auto de Fiscalização"
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Relatório Descritivo -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Relatório Descritivo da Ação</label>
                        <div class="relative">
                            <textarea name="observacao" id="observacao" rows="8" placeholder="Descreva detalhadamente a ação realizada..."
                                      class="w-full px-4 py-3 pr-12 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400 resize-y min-h-[200px]"></textarea>
                            <button type="button" onclick="startVoiceInput('observacao')"
                                    class="voice-btn absolute right-2 top-3 p-2 text-gray-500 dark:text-gray-400 hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Aba 4: Moradores -->
                <div class="tab-content p-4 space-y-4 hidden" data-tab="3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Moradores do Ponto</h3>
                            <button type="button" onclick="abrirModalMorador()"
                                    class="px-3 py-1.5 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Adicionar
                            </button>
                        </div>

                        <!-- Lista de moradores existentes do ponto -->
                        @if($pontoProximo && $pontoProximo->moradores->count() > 0)
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Moradores já cadastrados neste ponto:</p>
                                <div id="moradores-existentes" class="space-y-2">
                                    @foreach($pontoProximo->moradores as $morador)
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="flex-shrink-0">
                                                @if($morador->fotografia)
                                                    <img src="{{ Storage::url($morador->fotografia) }}" class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                    <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $morador->nome_social }}</p>
                                                @if($morador->apelido)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">"{{ $morador->apelido }}"</p>
                                                @endif
                                            </div>
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" name="moradores_presentes[]" value="{{ $morador->id }}" checked
                                                       class="rounded text-blue-500 w-5 h-5">
                                                <span class="text-xs text-gray-600 dark:text-gray-400">Presente</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="sem-moradores-msg">
                                @if($pontoProximo)
                                    Nenhum morador cadastrado neste ponto.
                                @else
                                    Os moradores serão vinculados ao novo ponto após o cadastro.
                                @endif
                            </p>
                        @endif

                        <!-- Novos moradores adicionados na vistoria -->
                        <div id="novos-moradores" class="space-y-2"></div>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center">
                            <span id="morador-count">0</span> novo(s) morador(es) a cadastrar
                        </p>
                    </div>
                </div>

                <!-- Aba 5: Fotos -->
                <div class="tab-content p-4 space-y-4 hidden" data-tab="4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Fotos da Vistoria</label>

                        <input type="file" id="camera-input-back" accept="image/*" capture="environment" class="hidden">
                        <input type="file" id="camera-input-front" accept="image/*" capture="user" class="hidden">
                        <input type="file" id="gallery-input" accept="image/*" multiple class="hidden">

                        <div class="space-y-2">
                            <button type="button" onclick="openCamera('back')"
                                    class="w-full py-3 px-4 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Câmera Traseira</span>
                            </button>

                            <button type="button" onclick="openCamera('front')"
                                    class="w-full py-3 px-4 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Câmera Frontal</span>
                            </button>

                            <button type="button" onclick="openGallery()"
                                    class="w-full py-3 px-4 bg-green-500 text-white rounded-lg font-medium hover:bg-green-600 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Galeria</span>
                            </button>
                        </div>

                        <div id="fotos-preview" class="mt-4 grid grid-cols-3 gap-2"></div>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center">
                            <span id="foto-count">0</span> foto(s) selecionada(s)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Barra de Ações Fixa -->
            <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 space-y-2">
                <!-- Navegação entre abas -->
                <div class="flex gap-2">
                    <button type="button" onclick="prevTab()" id="btn-prev"
                            class="flex-1 py-2 px-4 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition hidden">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Anterior
                    </button>
                    <button type="button" onclick="nextTab()" id="btn-next"
                            class="flex-1 py-2 px-4 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Próxima
                        <svg class="w-5 h-5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                <button type="submit"
                        class="w-full bg-blue-500 text-white py-3 rounded-lg font-semibold hover:bg-blue-600 transition active:scale-95">
                    Registrar Vistoria
                </button>
                <a href="{{ route('mapa.index') }}"
                   class="block w-full text-center bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-3 rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-gray-600 transition border border-gray-300 dark:border-gray-600">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        let currentTab = 0;
        const totalTabs = 5;
        let recognition = null;
        let activeInput = null;
        let fotosSelecionadas = [];
        let novosMoradores = [];
        const tiposAbrigo = @json($tiposAbrigo);

        document.addEventListener('DOMContentLoaded', function() {
            showTab(0);
        });

        function showTab(index) {
            currentTab = index;

            document.querySelectorAll('.tab-content').forEach((content, i) => {
                content.classList.toggle('hidden', i !== index);
            });

            document.querySelectorAll('.tab-btn').forEach((btn, i) => {
                if (i === index) {
                    btn.classList.add('border-blue-500', 'text-blue-500');
                    btn.classList.remove('border-transparent', 'text-gray-500');
                } else {
                    btn.classList.remove('border-blue-500', 'text-blue-500');
                    btn.classList.add('border-transparent', 'text-gray-500');
                }
            });

            document.getElementById('btn-prev').classList.toggle('hidden', index === 0);
            document.getElementById('btn-next').classList.toggle('hidden', index === totalTabs - 1);

            document.querySelector('.tab-content:not(.hidden)')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function nextTab() {
            if (currentTab < totalTabs - 1) showTab(currentTab + 1);
        }

        function prevTab() {
            if (currentTab > 0) showTab(currentTab - 1);
        }

        function toggleQtdCasais() {
            const checkbox = document.getElementById('checkbox_casal');
            const container = document.getElementById('qtd_casais_container');
            container.classList.toggle('hidden', !checkbox.checked);
            if (!checkbox.checked) document.getElementById('qtd_casais').value = 1;
        }

        function toggleQtdAnimais() {
            const checkbox = document.getElementById('checkbox_animais');
            const container = document.getElementById('qtd_animais_container');
            container.classList.toggle('hidden', !checkbox.checked);
            if (!checkbox.checked) document.getElementById('qtd_animais').value = 1;
        }

        function toggleConducaoObs() {
            const radioSim = document.querySelector('input[name="conducao_forcas_seguranca"][value="1"]');
            const container = document.getElementById('conducao_obs_container');
            container.classList.toggle('hidden', !radioSim.checked);
            if (!radioSim.checked) document.getElementById('conducao_forcas_observacao').value = '';
        }

        function toggleAutoNumero() {
            const radioSim = document.querySelector('input[name="auto_fiscalizacao_aplicado"][value="1"]');
            const container = document.getElementById('auto_numero_container');
            container.classList.toggle('hidden', !radioSim.checked);
            if (!radioSim.checked) document.getElementById('auto_fiscalizacao_numero').value = '';
        }

        function atualizarCamposAbrigos() {
            const qtd = parseInt(document.getElementById('qtd_abrigos').value) || 0;
            const container = document.getElementById('abrigos-container');
            const list = document.getElementById('abrigos-list');
            const tipoUnico = document.getElementById('tipo-abrigo-unico');

            if (qtd > 0) {
                container.classList.remove('hidden');
                tipoUnico.classList.add('hidden');
                list.innerHTML = '';
                for (let i = 0; i < qtd; i++) {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-2';
                    div.innerHTML = `
                        <span class="text-sm text-gray-700 dark:text-gray-300 w-6">${i + 1}.</span>
                        <select name="abrigos_tipos[]" class="flex-1 px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecione...</option>
                            ${tiposAbrigo.map(t => `<option value="${t.id}">${t.tipo_abrigo}</option>`).join('')}
                        </select>
                    `;
                    list.appendChild(div);
                }
            } else {
                container.classList.add('hidden');
                tipoUnico.classList.remove('hidden');
                list.innerHTML = '';
            }
        }

        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
                   (navigator.maxTouchPoints && navigator.maxTouchPoints > 2 && /MacIntel/.test(navigator.platform));
        }

        function openCameraWithAPI(type = 'back') {
            const facingMode = type === 'back' ? 'environment' : 'user';
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                openCameraInput(type);
                return;
            }

            navigator.mediaDevices.getUserMedia({
                video: { facingMode: facingMode, width: { ideal: 1920 }, height: { ideal: 1080 } }
            })
            .then(function(stream) {
                const video = document.createElement('video');
                video.srcObject = stream;
                video.autoplay = true;
                video.playsInline = true;
                video.style.width = '100%';
                video.style.maxHeight = '400px';
                video.style.objectFit = 'contain';

                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black/75 z-50 flex flex-col items-center justify-center p-4';
                modal.innerHTML = `
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Tire uma foto</h3>
                        <div id="camera-preview" class="mb-4 bg-black rounded-lg overflow-hidden"></div>
                        <div class="flex gap-2">
                            <button id="capture-btn" class="flex-1 bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600">Capturar</button>
                            <button id="cancel-camera-btn" class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-3 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600">Cancelar</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
                modal.querySelector('#camera-preview').appendChild(video);

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                modal.querySelector('#capture-btn').addEventListener('click', function() {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0);
                    canvas.toBlob(function(blob) {
                        const file = new File([blob], 'foto-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                        processPhotoFile(file);
                        stream.getTracks().forEach(track => track.stop());
                        document.body.removeChild(modal);
                    }, 'image/jpeg', 0.9);
                });

                modal.querySelector('#cancel-camera-btn').addEventListener('click', function() {
                    stream.getTracks().forEach(track => track.stop());
                    document.body.removeChild(modal);
                });
            })
            .catch(function() {
                openCameraInput(type);
            });
        }

        function openCameraInput(type = 'back') {
            const inputId = type === 'back' ? 'camera-input-back' : 'camera-input-front';
            const input = document.getElementById(inputId);
            if (input) {
                input.value = '';
                input.click();
            }
        }

        function openCamera(type = 'back') {
            if (isMobileDevice()) {
                openCameraWithAPI(type);
            } else {
                openCameraInput(type);
            }
        }

        function openGallery() {
            const input = document.getElementById('gallery-input');
            if (input) {
                input.value = '';
                input.click();
            }
        }

        function processPhotoFile(file) {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                fotosSelecionadas.push({ file: file, preview: e.target.result, id: Date.now() + Math.random() });
                renderFotosPreview();
            };
            reader.readAsDataURL(file);
        }

        document.getElementById('camera-input-back').addEventListener('change', function(e) {
            if (e.target.files[0]) processPhotoFile(e.target.files[0]);
            e.target.value = '';
        });

        document.getElementById('camera-input-front').addEventListener('change', function(e) {
            if (e.target.files[0]) processPhotoFile(e.target.files[0]);
            e.target.value = '';
        });

        document.getElementById('gallery-input').addEventListener('change', function(e) {
            Array.from(e.target.files).forEach(file => processPhotoFile(file));
            e.target.value = '';
        });

        function renderFotosPreview() {
            const container = document.getElementById('fotos-preview');
            container.innerHTML = '';
            fotosSelecionadas.forEach((foto, index) => {
                const div = document.createElement('div');
                div.className = 'relative group aspect-square';
                div.innerHTML = `
                    <img src="${foto.preview}" alt="Foto ${index + 1}" class="w-full h-full object-cover rounded-lg border-2 border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="removerFoto(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-80 hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;
                container.appendChild(div);
            });
            document.getElementById('foto-count').textContent = fotosSelecionadas.length;
        }

        function removerFoto(index) {
            fotosSelecionadas.splice(index, 1);
            renderFotosPreview();
        }

        document.getElementById('vistoria-form').addEventListener('submit', function(e) {
            fotosSelecionadas.forEach((foto) => {
                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'fotos[]';
                input.style.display = 'none';
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(foto.file);
                input.files = dataTransfer.files;
                this.appendChild(input);
            });
        });

        function startVoiceInput(inputId) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                alert('Seu navegador não suporta reconhecimento de voz.');
                return;
            }

            if (recognition && activeInput === inputId) {
                recognition.stop();
                return;
            }

            if (recognition) recognition.stop();

            recognition = new SpeechRecognition();
            recognition.lang = 'pt-BR';
            recognition.continuous = false;
            recognition.interimResults = true;

            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('.voice-btn');
            activeInput = inputId;

            button.classList.add('text-red-500', 'animate-pulse');
            button.classList.remove('text-gray-500');

            recognition.onresult = (event) => {
                let transcript = '';
                for (let i = 0; i < event.results.length; i++) {
                    transcript += event.results[i][0].transcript;
                }
                if (input.value && !input.value.endsWith('\n')) input.value += '\n';
                input.value = input.value.trimEnd() + (input.value ? '\n' : '') + transcript;
            };

            recognition.onend = () => {
                button.classList.remove('text-red-500', 'animate-pulse');
                button.classList.add('text-gray-500');
                activeInput = null;
            };

            recognition.onerror = () => {
                button.classList.remove('text-red-500', 'animate-pulse');
                button.classList.add('text-gray-500');
                activeInput = null;
            };

            recognition.start();
        }

        // Funções para gerenciar moradores
        function abrirModalMorador(index = null) {
            const modal = document.getElementById('modal-morador');
            const titulo = document.getElementById('modal-morador-titulo');

            // Limpar campos
            document.getElementById('morador-edit-index').value = index !== null ? index : '';
            document.getElementById('morador-nome-social').value = '';
            document.getElementById('morador-apelido').value = '';
            document.getElementById('morador-genero').value = '';
            document.getElementById('morador-documento').value = '';
            document.getElementById('morador-contato').value = '';
            document.getElementById('morador-observacoes').value = '';

            if (index !== null && novosMoradores[index]) {
                titulo.textContent = 'Editar Morador';
                const m = novosMoradores[index];
                document.getElementById('morador-nome-social').value = m.nome_social || '';
                document.getElementById('morador-apelido').value = m.apelido || '';
                document.getElementById('morador-genero').value = m.genero || '';
                document.getElementById('morador-documento').value = m.documento || '';
                document.getElementById('morador-contato').value = m.contato || '';
                document.getElementById('morador-observacoes').value = m.observacoes || '';
            } else {
                titulo.textContent = 'Novo Morador';
            }

            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Esconder toda a página de fundo
            document.getElementById('app').style.visibility = 'hidden';
            modal.style.visibility = 'visible';
        }

        function fecharModalMorador() {
            const modal = document.getElementById('modal-morador');
            modal.style.display = 'none';
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            // Mostrar página novamente
            document.getElementById('app').style.visibility = 'visible';
        }

        function salvarMorador() {
            const nome = document.getElementById('morador-nome-social').value.trim();
            if (!nome) {
                alert('Nome social é obrigatório');
                return;
            }

            const morador = {
                nome_social: nome,
                apelido: document.getElementById('morador-apelido').value.trim(),
                genero: document.getElementById('morador-genero').value,
                documento: document.getElementById('morador-documento').value.trim(),
                contato: document.getElementById('morador-contato').value.trim(),
                observacoes: document.getElementById('morador-observacoes').value.trim(),
                id: Date.now()
            };

            const editIndex = document.getElementById('morador-edit-index').value;
            if (editIndex !== '') {
                novosMoradores[parseInt(editIndex)] = morador;
            } else {
                novosMoradores.push(morador);
            }

            renderNovosMoradores();
            fecharModalMorador();
        }

        function removerMorador(index) {
            if (confirm('Remover este morador?')) {
                novosMoradores.splice(index, 1);
                renderNovosMoradores();
            }
        }

        function renderNovosMoradores() {
            const container = document.getElementById('novos-moradores');
            container.innerHTML = '';

            novosMoradores.forEach((m, index) => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg';
                div.innerHTML = `
                    <div class="w-10 h-10 rounded-full bg-green-200 dark:bg-green-800 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">${m.nome_social}</p>
                        ${m.apelido ? `<p class="text-xs text-gray-500 dark:text-gray-400">"${m.apelido}"</p>` : ''}
                        <span class="text-xs text-green-600 dark:text-green-400">Novo</span>
                    </div>
                    <div class="flex gap-1">
                        <button type="button" onclick="abrirModalMorador(${index})" class="p-2 text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button type="button" onclick="removerMorador(${index})" class="p-2 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    <input type="hidden" name="novos_moradores[${index}][nome_social]" value="${m.nome_social}">
                    <input type="hidden" name="novos_moradores[${index}][apelido]" value="${m.apelido || ''}">
                    <input type="hidden" name="novos_moradores[${index}][genero]" value="${m.genero || ''}">
                    <input type="hidden" name="novos_moradores[${index}][documento]" value="${m.documento || ''}">
                    <input type="hidden" name="novos_moradores[${index}][contato]" value="${m.contato || ''}">
                    <input type="hidden" name="novos_moradores[${index}][observacoes]" value="${m.observacoes || ''}">
                `;
                container.appendChild(div);
            });

            document.getElementById('morador-count').textContent = novosMoradores.length;
        }
    </script>
@endsection

@push('scripts')
<!-- Modal Adicionar/Editar Morador -->
<div id="modal-morador" onclick="if(event.target === this) fecharModalMorador()" class="fixed inset-0 bg-black/70 z-[99999] hidden items-end sm:items-center justify-center p-4" style="display: none;">
    <div class="bg-white dark:bg-gray-800 w-full sm:max-w-md sm:rounded-lg rounded-t-2xl max-h-[85vh] overflow-y-auto shadow-2xl" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
            <h3 id="modal-morador-titulo" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Novo Morador</h3>
            <button type="button" onclick="fecharModalMorador()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="form-morador" class="p-4 space-y-4">
            <input type="hidden" id="morador-edit-index" name="morador-edit-index" value="">

            <div>
                <label for="morador-nome-social" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Social *</label>
                <input type="text" id="morador-nome-social" name="morador-nome-social" placeholder="Como deseja ser chamado"
                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="morador-apelido" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apelido</label>
                <input type="text" id="morador-apelido" name="morador-apelido" placeholder="Como é conhecido"
                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="morador-genero" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gênero</label>
                <select id="morador-genero" name="morador-genero"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Prefiro não informar</option>
                    <option value="Homem cisgênero">Homem cisgênero</option>
                    <option value="Mulher cisgênero">Mulher cisgênero</option>
                    <option value="Homem trans">Homem trans</option>
                    <option value="Mulher trans">Mulher trans</option>
                    <option value="Travesti">Travesti</option>
                    <option value="Não-binário">Não-binário</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>

            <div>
                <label for="morador-documento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Documento</label>
                <input type="text" id="morador-documento" name="morador-documento" placeholder="CPF ou RG"
                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="morador-contato" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contato</label>
                <input type="text" id="morador-contato" name="morador-contato" placeholder="Telefone ou outro"
                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="morador-observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                <textarea id="morador-observacoes" name="morador-observacoes" rows="2" placeholder="Informações adicionais"
                          class="w-full px-4 py-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="salvarMorador()"
                        class="flex-1 py-3 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 transition">
                    Salvar
                </button>
                <button type="button" onclick="fecharModalMorador()"
                        class="flex-1 py-3 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 transition border border-gray-300 dark:border-gray-600">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
@endpush
