@extends('layouts.app')

@section('title', 'Nova Vistoria')

@section('header')
    <div class="flex items-center gap-3 flex-1">
        <a href="{{ route('mapa.index') }}" class="btn btn-ghost btn-icon" style="margin-left: -8px;">
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="mobile-header-title flex-1 text-center">Nova Vistoria</span>
        <div style="width: 44px;"></div>
    </div>
@endsection

@section('content')
    <div class="form-page">
        <form id="vistoria-form" action="{{ route('vistorias.store') }}" method="POST" enctype="multipart/form-data" class="form-container">
            @csrf
            <input type="hidden" name="lat" value="{{ $lat }}">
            <input type="hidden" name="lng" value="{{ $lng }}">
            @if($pontoProximo)
                <input type="hidden" name="ponto_id" value="{{ $pontoProximo->id }}">
            @endif

            <!-- Progress Stepper -->
            <div class="progress-stepper" id="progress-stepper">
                <div class="stepper-item active" data-step="0" onclick="goToStep(0)">
                    <div class="stepper-circle">1</div>
                    <span class="stepper-label">Dados</span>
                </div>
                <div class="stepper-item" data-step="1" onclick="goToStep(1)">
                    <div class="stepper-circle">2</div>
                    <span class="stepper-label">Caract.</span>
                </div>
                <div class="stepper-item" data-step="2" onclick="goToStep(2)">
                    <div class="stepper-circle">3</div>
                    <span class="stepper-label">Relatorio</span>
                </div>
                <div class="stepper-item" data-step="3" onclick="goToStep(3)">
                    <div class="stepper-circle">4</div>
                    <span class="stepper-label">Encam.</span>
                </div>
                <div class="stepper-item" data-step="4" onclick="goToStep(4)">
                    <div class="stepper-circle">5</div>
                    <span class="stepper-label">Moradores</span>
                </div>
                <div class="stepper-item" data-step="5" onclick="goToStep(5)">
                    <div class="stepper-circle">6</div>
                    <span class="stepper-label">Fotos</span>
                </div>
                <div class="stepper-item" data-step="6" onclick="goToStep(6)">
                    <div class="stepper-circle">7</div>
                    <span class="stepper-label">Revisar</span>
                </div>
            </div>
            <div class="step-indicator">
                <span id="step-indicator">Etapa <span class="step-indicator-text">1</span> de <span class="step-indicator-text">7</span></span>
            </div>

            <!-- Conteudo das Abas -->
            <div class="form-content">
                <!-- Aba 1: Dados Basicos -->
                <div class="tab-content" data-tab="0">
                    <!-- Localizacao -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">Localizacao</h3>
                            @if($pontoProximo)
                                <p class="text-success" style="font-size: var(--text-sm);">
                                    <span style="font-weight: var(--font-medium);">Ponto existente:</span>
                                    {{ $pontoProximo->enderecoAtualizado->tipo ?? '' }} {{ $pontoProximo->enderecoAtualizado->logradouro ?? '' }}, {{ $pontoProximo->enderecoAtualizado->numero ?? $pontoProximo->numero }} - {{ $pontoProximo->enderecoAtualizado->bairro ?? '' }}
                                </p>
                            @else
                                <div>
                                    <p class="text-warning" style="font-size: var(--text-sm); font-weight: var(--font-medium);">
                                        Novo ponto sera criado
                                    </p>
                                    @if($enderecoReferencia)
                                        <p class="text-secondary" style="font-size: var(--text-sm); margin-top: var(--space-1);">
                                            <span style="font-weight: var(--font-medium);">Referencia:</span>
                                            {{ $enderecoReferencia['tipo'] }} {{ $enderecoReferencia['logradouro'] }}, {{ $enderecoReferencia['numero'] }}
                                        </p>
                                        <p class="text-muted" style="font-size: var(--text-xs);">
                                            {{ $enderecoReferencia['bairro'] }} - {{ $enderecoReferencia['regional'] }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                            <p class="text-muted" style="font-size: var(--text-xs); margin-top: var(--space-1);">
                                Lat: {{ number_format($lat, 6) }} | Lng: {{ number_format($lng, 6) }}
                            </p>

                            <div class="form-group mt-3">
                                <label class="form-label">Referencia do Endereco</label>
                                <input type="text" name="complemento_ponto"
                                       value="{{ $pontoProximo->complemento ?? $referenciaAutomatica ?? '' }}"
                                       placeholder="Ex: Proximo ao mercado, em frente a escola..."
                                       class="form-input">
                                <p class="form-hint">Descricao do local para facilitar a identificacao</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dados da Vistoria -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">Dados da Vistoria</h3>

                            <div class="form-group">
                                <label class="form-label required">Data/Hora da Abordagem</label>
                                <input type="datetime-local" name="data_abordagem" value="{{ date('Y-m-d\TH:i') }}" required class="form-input">
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Tipo de Abordagem</label>
                                <select name="tipo_abordagem_id" required class="form-input form-select">
                                    <option value="">Selecione...</option>
                                    @foreach($tiposAbordagem as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba 2: Perfil da Ocorrencia -->
                <div class="tab-content hidden" data-tab="1">
                    <!-- Quantidades -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="form-group">
                                    <label class="form-label">Qtd. Pessoas</label>
                                    <input type="number" name="quantidade_pessoas" min="0" value="0" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Qtd. Kg</label>
                                    <input type="number" name="qtd_kg" min="0" value="0" class="form-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nomes das Pessoas -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label">Nomes das Pessoas</label>
                            <p class="form-hint">Um nome por linha</p>
                            <div class="input-with-voice">
                                <textarea name="nomes_pessoas" id="nomes_pessoas" rows="3" placeholder="Digite um nome por linha..." class="form-input form-textarea"></textarea>
                                <button type="button" onclick="startVoiceInput('nomes_pessoas')" class="voice-btn">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Abrigos -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">Abrigos</h3>

                            <div class="form-group">
                                <label class="form-label">Qtd. Abrigos Provisorios</label>
                                <input type="number" name="qtd_abrigos_provisorios" id="qtd_abrigos" min="0" value="0" onchange="atualizarCamposAbrigos()" class="form-input">
                            </div>

                            <div id="abrigos-container" class="hidden">
                                <label class="form-label">Tipos de Abrigo Desmontado</label>
                                <div id="abrigos-list" class="flex flex-col gap-2"></div>
                            </div>

                            <div id="tipo-abrigo-unico" class="form-group">
                                <label class="form-label">Tipo de Abrigo Desmontado</label>
                                <select name="tipo_abrigo_desmontado_id" class="form-input form-select">
                                    <option value="">Nenhum</option>
                                    @foreach($tiposAbrigo as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->tipo_abrigo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Fatores de Complexidade -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">Fatores de Complexidade</h3>
                            <div class="checkbox-grid">
                                <label class="checkbox-card">
                                    <input type="checkbox" name="resistencia" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <span>Resistencia</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="num_reduzido" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Num. Reduzido</span>
                                </label>
                                <label class="checkbox-card checkbox-card-expandable">
                                    <input type="checkbox" name="casal" id="checkbox_casal" value="1" onchange="toggleQtdCasais()" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    <span>Casal</span>
                                    <input type="number" name="qtd_casais" id="qtd_casais" min="1" value="1" placeholder="Qtd" class="form-input form-input-sm checkbox-qty-input hidden" onclick="event.stopPropagation()">
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="catador_reciclados" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span>Catador</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="fixacao_antiga" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Fixacao Antiga</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="excesso_objetos" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <span>Excesso Objetos</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="trafico_ilicitos" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span>Trafico/Ilicitos</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="crianca_adolescente" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Crianca/Adolesc.</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="idosos" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span>Idosos</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="gestante" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2a3 3 0 100 6 3 3 0 000-6zm-1 8c-2.5 0-4 1.5-4 4v1h2v5h6v-5h2v-1c0-2.5-1.5-4-4-4h-2z"/>
                                    </svg>
                                    <span>Gestante</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="lgbtqiapn" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                    <span>LGBTQIAPN+</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="deficiente" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2a3 3 0 100 6 3 3 0 000-6zm-2 8l-4 4h3v6h6v-6h3l-4-4h-4z"/>
                                    </svg>
                                    <span>Deficiente</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="agrupamento_quimico" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                    <span>Agrup. Quimico</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="saude_mental" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <span>Saude Mental</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="cena_uso_caracterizada" value="1" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    <span>Cena de Uso</span>
                                </label>
                                <label class="checkbox-card checkbox-card-expandable">
                                    <input type="checkbox" name="animais" id="checkbox_animais" value="1" onchange="toggleQtdAnimais()" class="form-checkbox">
                                    <svg class="checkbox-icon" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4.5 9.5a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0zm9 0a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0zm-7.5 6a2 2 0 114 0 2 2 0 01-4 0zm7 0a2 2 0 114 0 2 2 0 01-4 0zm-3.5 2.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V16h-5v2z"/>
                                    </svg>
                                    <span>Animais</span>
                                    <input type="number" name="qtd_animais" id="qtd_animais" min="1" value="1" placeholder="Qtd" class="form-input form-input-sm checkbox-qty-input hidden" onclick="event.stopPropagation()">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba 3: Relatorio da Acao -->
                <div class="tab-content hidden" data-tab="2">
                    <!-- Resultado da Acao -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label required">Resultado da Acao</label>
                                <select name="resultado_acao_id" required class="form-input form-select">
                                    <option value="">Selecione...</option>
                                    @foreach($resultadosAcao as $resultado)
                                        @if($pontoProximo || !str_contains(strtolower($resultado->resultado), 'persiste'))
                                            <option value="{{ $resultado->id }}">{{ $resultado->resultado }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">Acoes Realizadas</h3>

                            <!-- Conducao pelas Forcas de Seguranca -->
                            <div class="form-group">
                                <label class="form-label">Conducao pelas Forcas de Seguranca</label>
                                <div class="radio-group">
                                    <label class="radio-option">
                                        <input type="radio" name="conducao_forcas_seguranca" value="1" onchange="toggleConducaoObs()" class="form-radio">
                                        <span>Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="conducao_forcas_seguranca" value="0" checked onchange="toggleConducaoObs()" class="form-radio">
                                        <span>Nao</span>
                                    </label>
                                </div>
                                <div id="conducao_obs_container" class="mt-2 hidden">
                                    <textarea name="conducao_forcas_observacao" id="conducao_forcas_observacao" rows="2" placeholder="Observacao sobre a conducao..." class="form-input form-textarea"></textarea>
                                </div>
                            </div>

                            <!-- Apreensao Fiscal -->
                            <div class="form-group">
                                <label class="checkbox-card">
                                    <input type="checkbox" name="apreensao_fiscal" value="1" class="form-checkbox">
                                    <span>Apreensao Fiscal</span>
                                </label>
                            </div>

                            <!-- Auto de Fiscalizacao Aplicado -->
                            <div class="form-group">
                                <label class="form-label">Auto de Fiscalizacao Aplicado</label>
                                <div class="radio-group">
                                    <label class="radio-option">
                                        <input type="radio" name="auto_fiscalizacao_aplicado" value="1" onchange="toggleAutoNumero()" class="form-radio">
                                        <span>Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="auto_fiscalizacao_aplicado" value="0" checked onchange="toggleAutoNumero()" class="form-radio">
                                        <span>Nao</span>
                                    </label>
                                </div>
                                <div id="auto_numero_container" class="mt-2 hidden">
                                    <input type="text" name="auto_fiscalizacao_numero" id="auto_fiscalizacao_numero" placeholder="Numero do Auto de Fiscalizacao" class="form-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Relatorio Descritivo -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label">Relatorio Descritivo da Acao</label>
                            <div class="input-with-voice">
                                <textarea name="observacao" id="observacao" rows="8" placeholder="Descreva detalhadamente a acao realizada..." class="form-input form-textarea" style="min-height: 200px;"></textarea>
                                <button type="button" onclick="startVoiceInput('observacao')" class="voice-btn">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba 4: Encaminhamentos -->
                <div class="tab-content hidden" data-tab="3">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">Encaminhamentos</h3>
                            <p class="text-muted mb-4" style="font-size: var(--text-sm);">
                                Selecione os encaminhamentos realizados durante esta vistoria (opcional).
                            </p>
                            <div class="flex flex-col gap-3">
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 1</label>
                                    <select name="e1_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}">{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 2</label>
                                    <select name="e2_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}">{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 3</label>
                                    <select name="e3_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}">{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 4</label>
                                    <select name="e4_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}">{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 5</label>
                                    <select name="e5_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}">{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 6</label>
                                    <select name="e6_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}">{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba 5: Moradores -->
                <div class="tab-content hidden" data-tab="4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="form-section-title" style="margin-bottom: 0;">Moradores do Ponto</h3>
                                <button type="button" onclick="abrirModalMorador()" class="btn btn-primary btn-sm">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Adicionar
                                </button>
                            </div>

                            @if($pontoProximo && $pontoProximo->moradores->count() > 0)
                                <div class="mb-4">
                                    <p class="text-muted mb-2" style="font-size: var(--text-xs);">Moradores ja cadastrados neste ponto:</p>
                                    <div id="moradores-existentes" class="flex flex-col gap-2">
                                        @foreach($pontoProximo->moradores as $morador)
                                            <div class="morador-card">
                                                <div class="morador-avatar">
                                                    @if($morador->fotografia)
                                                        <img src="{{ Storage::url($morador->fotografia) }}" alt="{{ $morador->nome_social }}">
                                                    @else
                                                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="morador-info">
                                                    <p class="morador-name">{{ $morador->nome_social }}</p>
                                                    @if($morador->apelido)
                                                        <p class="morador-nickname">"{{ $morador->apelido }}"</p>
                                                    @endif
                                                </div>
                                                <label class="morador-presence">
                                                    <input type="checkbox" name="moradores_presentes[]" value="{{ $morador->id }}" checked class="form-checkbox">
                                                    <span>Presente</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="text-muted mb-4" id="sem-moradores-msg" style="font-size: var(--text-sm);">
                                    @if($pontoProximo)
                                        Nenhum morador cadastrado neste ponto.
                                    @else
                                        Os moradores serao vinculados ao novo ponto apos o cadastro.
                                    @endif
                                </p>
                            @endif

                            <div id="novos-moradores" class="flex flex-col gap-2"></div>

                            <p class="text-muted mt-3 text-center" style="font-size: var(--text-xs);">
                                <span id="morador-count">0</span> novo(s) morador(es) a cadastrar
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Aba 6: Fotos -->
                <div class="tab-content hidden" data-tab="5">
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label mb-3">Fotos da Vistoria</label>

                            <input type="file" id="camera-input-back" accept="image/*" capture="environment" class="hidden">
                            <input type="file" id="camera-input-front" accept="image/*" capture="user" class="hidden">
                            <input type="file" id="gallery-input" accept="image/*" multiple class="hidden">

                            <div class="flex flex-col gap-2">
                                <button type="button" onclick="openCamera('back')" class="btn btn-primary btn-block">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Camera Traseira
                                </button>

                                <button type="button" onclick="openCamera('front')" class="btn btn-secondary btn-block">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Camera Frontal
                                </button>

                                <button type="button" onclick="openGallery()" class="btn btn-success btn-block">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Galeria
                                </button>
                            </div>

                            <div id="fotos-preview" class="photos-grid mt-4"></div>

                            <p class="text-muted mt-3 text-center" style="font-size: var(--text-xs);">
                                <span id="foto-count">0</span> foto(s) selecionada(s)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Aba 7: Revisar e Finalizar -->
                <div class="tab-content hidden" data-tab="6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Revisao da Vistoria
                            </h3>
                            <p class="text-muted mb-4" style="font-size: var(--text-sm);">Verifique os dados antes de finalizar.</p>

                            <div id="review-checklist" class="review-checklist"></div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body" style="text-align: center;">
                            <div id="review-status" class="mb-4"></div>
                            <button type="submit" id="btn-submit" class="btn btn-primary btn-block btn-lg">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Registrar Vistoria
                            </button>
                            <a href="{{ route('mapa.index') }}" class="btn btn-ghost btn-block mt-2">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let currentTab = 0;
        const totalTabs = 7;
        let visitedSteps = new Set([0]);
        let recognition = null;
        let activeInput = null;
        let fotosSelecionadas = [];
        let novosMoradores = [];
        const tiposAbrigo = @json($tiposAbrigo);

        const stepLabels = ['Dados', 'Caract.', 'Relatorio', 'Encam.', 'Moradores', 'Fotos', 'Revisar'];
        const checkmarkSVG = '<svg class="stepper-check" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';

        document.addEventListener('DOMContentLoaded', function() {
            showTab(0);
        });

        function updateStepper(currentIndex) {
            visitedSteps.add(currentIndex);

            document.querySelectorAll('.stepper-item').forEach((item, i) => {
                const circle = item.querySelector('.stepper-circle');
                item.classList.remove('active', 'visited', 'completed');

                if (i === currentIndex) {
                    item.classList.add('active');
                    circle.innerHTML = i + 1;
                } else if (visitedSteps.has(i)) {
                    item.classList.add('visited');
                    circle.innerHTML = checkmarkSVG;
                } else {
                    circle.innerHTML = i + 1;
                }
            });

            document.getElementById('step-indicator').innerHTML =
                `Etapa <span class="step-indicator-text">${currentIndex + 1}</span> de <span class="step-indicator-text">${totalTabs}</span> - ${stepLabels[currentIndex]}`;
        }

        function showTab(index) {
            currentTab = index;
            updateStepper(index);

            document.querySelectorAll('.tab-content').forEach((content, i) => {
                content.classList.toggle('hidden', i !== index);
            });

            // Ao entrar na aba de revisao, montar checklist
            if (index === 6) {
                buildReviewChecklist();
            }

            document.querySelector('.form-content')?.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function goToStep(index) {
            showTab(index);
        }

        function buildReviewChecklist() {
            const container = document.getElementById('review-checklist');
            const statusEl = document.getElementById('review-status');
            const btnSubmit = document.getElementById('btn-submit');

            const checks = [
                {
                    label: 'Data/Hora da Abordagem',
                    step: 0,
                    check: () => !!document.querySelector('[name="data_abordagem"]')?.value
                },
                {
                    label: 'Tipo de Abordagem',
                    step: 0,
                    check: () => {
                        const v = document.querySelector('[name="tipo_abordagem_id"]')?.value;
                        return v && v !== '';
                    }
                },
                {
                    label: 'Resultado da Acao',
                    step: 2,
                    check: () => {
                        const v = document.querySelector('[name="resultado_acao_id"]')?.value;
                        return v && v !== '';
                    }
                },
                {
                    label: 'Quantidade de Pessoas',
                    step: 0,
                    check: () => {
                        const v = document.querySelector('[name="qtd_pessoas"]')?.value;
                        return v && parseInt(v) > 0;
                    },
                    optional: true
                },
                {
                    label: 'Observacoes preenchidas',
                    step: 2,
                    check: () => !!document.querySelector('[name="observacoes"]')?.value?.trim(),
                    optional: true
                },
                {
                    label: 'Fotos anexadas',
                    step: 5,
                    check: () => fotosSelecionadas.length > 0,
                    optional: true
                }
            ];

            let html = '';
            let allRequiredOk = true;

            checks.forEach(item => {
                const ok = item.check();
                if (!item.optional && !ok) allRequiredOk = false;

                const icon = ok
                    ? '<svg style="width:20px;height:20px;color:var(--color-success);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                    : item.optional
                        ? '<svg style="width:20px;height:20px;color:var(--text-muted);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>'
                        : '<svg style="width:20px;height:20px;color:var(--color-danger);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>';

                const tag = !ok && !item.optional
                    ? `<span class="badge badge-danger" style="font-size:10px;cursor:pointer;" onclick="goToStep(${item.step})">Ir para etapa ${item.step + 1}</span>`
                    : item.optional && !ok
                        ? '<span class="badge" style="font-size:10px;">Opcional</span>'
                        : '';

                html += `
                    <div class="review-item ${ok ? 'ok' : (!item.optional ? 'missing' : 'optional')}">
                        ${icon}
                        <span class="review-item-label">${item.label}</span>
                        ${tag}
                    </div>`;
            });

            container.innerHTML = html;

            if (allRequiredOk) {
                statusEl.innerHTML = '<div class="alert alert-success" style="margin:0;"><strong>Tudo certo!</strong> A vistoria esta pronta para ser registrada.</div>';
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('btn-disabled');
            } else {
                statusEl.innerHTML = '<div class="alert alert-danger" style="margin:0;"><strong>Campos obrigatorios pendentes.</strong> Corrija antes de finalizar.</div>';
                btnSubmit.disabled = true;
                btnSubmit.classList.add('btn-disabled');
            }
        }

        function checkRequiredFields(stepIndex) {
            const stepContent = document.querySelector(`.tab-content[data-tab="${stepIndex}"]`);
            if (!stepContent) return [];

            const requiredFields = stepContent.querySelectorAll('[required]');
            let missingFields = [];

            requiredFields.forEach(field => {
                if (!field.value || field.value === '') {
                    const label = field.closest('.form-group')?.querySelector('.form-label');
                    const fieldName = label ? label.textContent.replace(' *', '').trim() : 'Campo';
                    missingFields.push(fieldName);
                }
            });

            return missingFields;
        }

        function showToast(message, type = 'info') {
            const existingToast = document.querySelector('.toast');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function toggleQtdCasais() {
            const checkbox = document.getElementById('checkbox_casal');
            const input = document.getElementById('qtd_casais');
            input.classList.toggle('hidden', !checkbox.checked);
            if (!checkbox.checked) input.value = 1;
        }

        function toggleQtdAnimais() {
            const checkbox = document.getElementById('checkbox_animais');
            const input = document.getElementById('qtd_animais');
            input.classList.toggle('hidden', !checkbox.checked);
            if (!checkbox.checked) input.value = 1;
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
                        <span class="text-muted" style="width: 24px;">${i + 1}.</span>
                        <select name="abrigos_tipos[]" class="form-input form-select flex-1">
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
                modal.className = 'modal-overlay';
                modal.innerHTML = `
                    <div class="modal">
                        <div class="modal-header">
                            <h3 class="modal-title">Tire uma foto</h3>
                        </div>
                        <div class="modal-body">
                            <div id="camera-preview" style="background: black; border-radius: var(--card-radius); overflow: hidden;"></div>
                        </div>
                        <div class="modal-footer">
                            <button id="capture-btn" class="btn btn-primary flex-1">Capturar</button>
                            <button id="cancel-camera-btn" class="btn btn-secondary flex-1">Cancelar</button>
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
                div.className = 'photo-preview';
                div.innerHTML = `
                    <img src="${foto.preview}" alt="Foto ${index + 1}">
                    <button type="button" onclick="removerFoto(${index})" class="photo-remove-btn">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                alert('Seu navegador nao suporta reconhecimento de voz.');
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

            button.classList.add('recording');

            recognition.onresult = (event) => {
                let transcript = '';
                for (let i = 0; i < event.results.length; i++) {
                    transcript += event.results[i][0].transcript;
                }
                if (input.value && !input.value.endsWith('\n')) input.value += '\n';
                input.value = input.value.trimEnd() + (input.value ? '\n' : '') + transcript;
            };

            recognition.onend = () => {
                button.classList.remove('recording');
                activeInput = null;
            };

            recognition.onerror = () => {
                button.classList.remove('recording');
                activeInput = null;
            };

            recognition.start();
        }

        // Funcoes para gerenciar moradores
        function abrirModalMorador(index = null) {
            const modal = document.getElementById('modal-morador');
            const titulo = document.getElementById('modal-morador-titulo');

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

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function fecharModalMorador() {
            const modal = document.getElementById('modal-morador');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function salvarMorador() {
            const nome = document.getElementById('morador-nome-social').value.trim();
            if (!nome) {
                alert('Nome social e obrigatorio');
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
                div.className = 'morador-card morador-card-new';
                div.innerHTML = `
                    <div class="morador-avatar morador-avatar-new">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="morador-info">
                        <p class="morador-name">${m.nome_social}</p>
                        ${m.apelido ? `<p class="morador-nickname">"${m.apelido}"</p>` : ''}
                        <span class="badge badge-success">Novo</span>
                    </div>
                    <div class="morador-actions">
                        <button type="button" onclick="abrirModalMorador(${index})" class="btn btn-ghost btn-icon btn-sm">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button type="button" onclick="removerMorador(${index})" class="btn btn-ghost btn-icon btn-sm text-danger">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Modal Adicionar/Editar Morador -->
    <div id="modal-morador" class="modal-overlay hidden" onclick="if(event.target === this) fecharModalMorador()">
        <div class="modal modal-bottom" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modal-morador-titulo" class="modal-title">Novo Morador</h3>
                <button type="button" onclick="fecharModalMorador()" class="btn btn-ghost btn-icon btn-sm">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="morador-edit-index" value="">

                <div class="form-group">
                    <label for="morador-nome-social" class="form-label required">Nome Social</label>
                    <input type="text" id="morador-nome-social" placeholder="Como deseja ser chamado" class="form-input">
                </div>

                <div class="form-group">
                    <label for="morador-apelido" class="form-label">Apelido</label>
                    <input type="text" id="morador-apelido" placeholder="Como e conhecido" class="form-input">
                </div>

                <div class="form-group">
                    <label for="morador-genero" class="form-label">Genero</label>
                    <select id="morador-genero" class="form-input form-select">
                        <option value="">Prefiro nao informar</option>
                        <option value="Homem cisgenero">Homem cisgenero</option>
                        <option value="Mulher cisgenero">Mulher cisgenero</option>
                        <option value="Homem trans">Homem trans</option>
                        <option value="Mulher trans">Mulher trans</option>
                        <option value="Travesti">Travesti</option>
                        <option value="Nao-binario">Nao-binario</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="morador-documento" class="form-label">Documento</label>
                    <input type="text" id="morador-documento" placeholder="CPF ou RG" class="form-input">
                </div>

                <div class="form-group">
                    <label for="morador-contato" class="form-label">Contato</label>
                    <input type="text" id="morador-contato" placeholder="Telefone ou outro" class="form-input">
                </div>

                <div class="form-group">
                    <label for="morador-observacoes" class="form-label">Observacoes</label>
                    <textarea id="morador-observacoes" rows="2" placeholder="Informacoes adicionais" class="form-input form-textarea"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="salvarMorador()" class="btn btn-primary flex-1">Salvar</button>
                <button type="button" onclick="fecharModalMorador()" class="btn btn-secondary flex-1">Cancelar</button>
            </div>
        </div>
    </div>
@endsection
