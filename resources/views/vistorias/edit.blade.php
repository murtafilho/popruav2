@extends('layouts.app')

@section('title', 'Editar Vistoria')

@section('header')
    <div class="flex items-center gap-3 flex-1">
        <a href="{{ route('vistorias.show', $vistoria) }}" class="btn btn-ghost btn-icon" style="margin-left: -8px;">
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="mobile-header-title flex-1 text-center">Editar Vistoria</span>
        <div style="width: 44px;"></div>
    </div>
@endsection

@section('content')
    <div class="form-page">
        <form id="vistoria-form" action="{{ route('vistorias.update', $vistoria) }}" method="POST" enctype="multipart/form-data" class="form-container">
            @csrf
            @method('PUT')

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
                    <span class="stepper-label">Fotos</span>
                </div>
                <div class="stepper-item" data-step="5" onclick="goToStep(5)">
                    <div class="stepper-circle">6</div>
                    <span class="stepper-label">Revisar</span>
                </div>
            </div>
            <div class="step-indicator">
                <span id="step-indicator">Etapa <span class="step-indicator-text">1</span> de <span class="step-indicator-text">6</span></span>
            </div>

            <!-- Conteudo das Abas -->
            <div class="form-content">
                <!-- Aba 1: Dados Basicos -->
                <div class="tab-content" data-tab="0">
                    <!-- Localizacao (somente leitura) -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">Localizacao</h3>
                            @if($vistoria->ponto && $vistoria->ponto->enderecoAtualizado)
                                <p style="font-size: var(--text-sm);">
                                    <span style="font-weight: var(--font-medium);">
                                        {{ $vistoria->ponto->enderecoAtualizado->SIGLA_TIPO_LOGRADOURO }}
                                        {{ $vistoria->ponto->enderecoAtualizado->NOME_LOGRADOURO }},
                                        {{ $vistoria->ponto->enderecoAtualizado->NUMERO_IMOVEL ?? $vistoria->ponto->numero }}
                                    </span>
                                </p>
                                <p class="text-muted" style="font-size: var(--text-xs);">
                                    {{ $vistoria->ponto->enderecoAtualizado->NOME_BAIRRO_OFICIAL }} - {{ $vistoria->ponto->enderecoAtualizado->NOME_REGIONAL }}
                                </p>
                            @endif
                            @if($vistoria->ponto && $vistoria->ponto->lat && $vistoria->ponto->lng)
                                <p class="text-muted" style="font-size: var(--text-xs); margin-top: var(--space-1);">
                                    Lat: {{ number_format($vistoria->ponto->lat, 6) }} | Lng: {{ number_format($vistoria->ponto->lng, 6) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Dados da Vistoria -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">Dados da Vistoria</h3>

                            <div class="form-group">
                                <label class="form-label required">Data/Hora da Abordagem</label>
                                <input type="datetime-local" name="data_abordagem"
                                       value="{{ $vistoria->data_abordagem ? $vistoria->data_abordagem->format('Y-m-d\TH:i') : '' }}"
                                       required class="form-input">
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Tipo de Abordagem</label>
                                <select name="tipo_abordagem_id" required class="form-input form-select">
                                    <option value="">Selecione...</option>
                                    @foreach($tiposAbordagem as $tipo)
                                        <option value="{{ $tipo->id }}" {{ $vistoria->tipo_abordagem_id == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->tipo }}
                                        </option>
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
                                    <input type="number" name="quantidade_pessoas" min="0"
                                           value="{{ $vistoria->quantidade_pessoas ?? 0 }}" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Qtd. Kg</label>
                                    <input type="number" name="qtd_kg" min="0"
                                           value="{{ $vistoria->qtd_kg ?? 0 }}" class="form-input">
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
                                <textarea name="nomes_pessoas" id="nomes_pessoas" rows="3" placeholder="Digite um nome por linha..." class="form-input form-textarea">{{ $vistoria->nomes_pessoas }}</textarea>
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
                                <input type="number" name="qtd_abrigos_provisorios" id="qtd_abrigos" min="0"
                                       value="{{ $vistoria->qtd_abrigos_provisorios ?? 0 }}"
                                       onchange="atualizarCamposAbrigos()" class="form-input">
                            </div>

                            <div id="abrigos-container" class="{{ ($vistoria->qtd_abrigos_provisorios ?? 0) > 0 ? '' : 'hidden' }}">
                                <label class="form-label">Tipos de Abrigo Desmontado</label>
                                <div id="abrigos-list" class="flex flex-col gap-2"></div>
                            </div>

                            <div id="tipo-abrigo-unico" class="form-group {{ ($vistoria->qtd_abrigos_provisorios ?? 0) > 0 ? 'hidden' : '' }}">
                                <label class="form-label">Tipo de Abrigo Desmontado</label>
                                <select name="tipo_abrigo_desmontado_id" class="form-input form-select">
                                    <option value="">Nenhum</option>
                                    @foreach($tiposAbrigo as $tipo)
                                        <option value="{{ $tipo->id }}" {{ $vistoria->tipo_abrigo_desmontado_id == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->tipo_abrigo }}
                                        </option>
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
                                    <input type="checkbox" name="resistencia" value="1" {{ $vistoria->resistencia ? 'checked' : '' }} class="form-checkbox">
                                    <span>Resistencia</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="num_reduzido" value="1" {{ $vistoria->num_reduzido ? 'checked' : '' }} class="form-checkbox">
                                    <span>Num. Reduzido</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="casal" id="checkbox_casal" value="1" {{ $vistoria->casal ? 'checked' : '' }} onchange="toggleQtdCasais()" class="form-checkbox">
                                    <span>Casal</span>
                                </label>
                                <div id="qtd_casais_container" class="{{ $vistoria->casal ? '' : 'hidden' }}">
                                    <input type="number" name="qtd_casais" id="qtd_casais" min="1"
                                           value="{{ $vistoria->qtd_casais ?? 1 }}" placeholder="Qtd." class="form-input form-input-sm">
                                </div>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="catador_reciclados" value="1" {{ $vistoria->catador_reciclados ? 'checked' : '' }} class="form-checkbox">
                                    <span>Catador</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="fixacao_antiga" value="1" {{ $vistoria->fixacao_antiga ? 'checked' : '' }} class="form-checkbox">
                                    <span>Fixacao Antiga</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="excesso_objetos" value="1" {{ $vistoria->excesso_objetos ? 'checked' : '' }} class="form-checkbox">
                                    <span>Excesso Objetos</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="trafico_ilicitos" value="1" {{ $vistoria->trafico_ilicitos ? 'checked' : '' }} class="form-checkbox">
                                    <span>Trafico/Ilicitos</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="crianca_adolescente" value="1" {{ $vistoria->crianca_adolescente ? 'checked' : '' }} class="form-checkbox">
                                    <span>Crianca/Adolesc.</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="idosos" value="1" {{ $vistoria->idosos ? 'checked' : '' }} class="form-checkbox">
                                    <span>Idosos</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="gestante" value="1" {{ $vistoria->gestante ? 'checked' : '' }} class="form-checkbox">
                                    <span>Gestante</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="lgbtqiapn" value="1" {{ $vistoria->lgbtqiapn ? 'checked' : '' }} class="form-checkbox">
                                    <span>LGBTQIAPN+</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="deficiente" value="1" {{ $vistoria->deficiente ? 'checked' : '' }} class="form-checkbox">
                                    <span>Deficiente</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="agrupamento_quimico" value="1" {{ $vistoria->agrupamento_quimico ? 'checked' : '' }} class="form-checkbox">
                                    <span>Agrup. Quimico</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="saude_mental" value="1" {{ $vistoria->saude_mental ? 'checked' : '' }} class="form-checkbox">
                                    <span>Saude Mental</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="cena_uso_caracterizada" value="1" {{ $vistoria->cena_uso_caracterizada ? 'checked' : '' }} class="form-checkbox">
                                    <span>Cena de Uso</span>
                                </label>
                                <label class="checkbox-card">
                                    <input type="checkbox" name="animais" id="checkbox_animais" value="1" {{ $vistoria->animais ? 'checked' : '' }} onchange="toggleQtdAnimais()" class="form-checkbox">
                                    <span>Animais</span>
                                </label>
                                <div id="qtd_animais_container" class="{{ $vistoria->animais ? '' : 'hidden' }}">
                                    <input type="number" name="qtd_animais" id="qtd_animais" min="1"
                                           value="{{ $vistoria->qtd_animais ?? 1 }}" placeholder="Qtd." class="form-input form-input-sm">
                                </div>
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
                                        <option value="{{ $resultado->id }}" {{ $vistoria->resultado_acao_id == $resultado->id ? 'selected' : '' }}>
                                            {{ $resultado->resultado }}
                                        </option>
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
                                        <input type="radio" name="conducao_forcas_seguranca" value="1" {{ $vistoria->conducao_forcas_seguranca ? 'checked' : '' }} onchange="toggleConducaoObs()" class="form-radio">
                                        <span>Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="conducao_forcas_seguranca" value="0" {{ !$vistoria->conducao_forcas_seguranca ? 'checked' : '' }} onchange="toggleConducaoObs()" class="form-radio">
                                        <span>Nao</span>
                                    </label>
                                </div>
                                <div id="conducao_obs_container" class="mt-2 {{ $vistoria->conducao_forcas_seguranca ? '' : 'hidden' }}">
                                    <textarea name="conducao_forcas_observacao" id="conducao_forcas_observacao" rows="2" placeholder="Observacao sobre a conducao..." class="form-input form-textarea">{{ $vistoria->conducao_forcas_observacao }}</textarea>
                                </div>
                            </div>

                            <!-- Apreensao Fiscal -->
                            <div class="form-group">
                                <label class="checkbox-card">
                                    <input type="checkbox" name="apreensao_fiscal" value="1" {{ $vistoria->apreensao_fiscal ? 'checked' : '' }} class="form-checkbox">
                                    <span>Apreensao Fiscal</span>
                                </label>
                            </div>

                            <!-- Auto de Fiscalizacao Aplicado -->
                            <div class="form-group">
                                <label class="form-label">Auto de Fiscalizacao Aplicado</label>
                                <div class="radio-group">
                                    <label class="radio-option">
                                        <input type="radio" name="auto_fiscalizacao_aplicado" value="1" {{ $vistoria->auto_fiscalizacao_aplicado ? 'checked' : '' }} onchange="toggleAutoNumero()" class="form-radio">
                                        <span>Sim</span>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="auto_fiscalizacao_aplicado" value="0" {{ !$vistoria->auto_fiscalizacao_aplicado ? 'checked' : '' }} onchange="toggleAutoNumero()" class="form-radio">
                                        <span>Nao</span>
                                    </label>
                                </div>
                                <div id="auto_numero_container" class="mt-2 {{ $vistoria->auto_fiscalizacao_aplicado ? '' : 'hidden' }}">
                                    <input type="text" name="auto_fiscalizacao_numero" id="auto_fiscalizacao_numero"
                                           value="{{ $vistoria->auto_fiscalizacao_numero }}"
                                           placeholder="Numero do Auto de Fiscalizacao" class="form-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Encaminhamentos -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">Encaminhamentos</h3>

                            @for($i = 1; $i <= 4; $i++)
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento {{ $i }}</label>
                                    <select name="e{{ $i }}_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $enc)
                                            <option value="{{ $enc->id }}" {{ $vistoria->{'e'.$i.'_id'} == $enc->id ? 'selected' : '' }}>
                                                {{ $enc->encaminhamento }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Relatorio Descritivo -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label">Relatorio Descritivo da Acao</label>
                            <div class="input-with-voice">
                                <textarea name="observacao" id="observacao" rows="8" placeholder="Descreva detalhadamente a acao realizada..." class="form-input form-textarea" style="min-height: 200px;">{{ $vistoria->observacao }}</textarea>
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
                                            <option value="{{ $encaminhamento->id }}" {{ $vistoria->e1_id == $encaminhamento->id ? 'selected' : '' }}>{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 2</label>
                                    <select name="e2_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}" {{ $vistoria->e2_id == $encaminhamento->id ? 'selected' : '' }}>{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 3</label>
                                    <select name="e3_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}" {{ $vistoria->e3_id == $encaminhamento->id ? 'selected' : '' }}>{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 4</label>
                                    <select name="e4_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}" {{ $vistoria->e4_id == $encaminhamento->id ? 'selected' : '' }}>{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 5</label>
                                    <select name="e5_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}" {{ $vistoria->e5_id == $encaminhamento->id ? 'selected' : '' }}>{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Encaminhamento 6</label>
                                    <select name="e6_id" class="form-input form-select">
                                        <option value="">Nenhum</option>
                                        @foreach($encaminhamentos as $encaminhamento)
                                            <option value="{{ $encaminhamento->id }}" {{ $vistoria->e6_id == $encaminhamento->id ? 'selected' : '' }}>{{ $encaminhamento->encaminhamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba 5: Fotos -->
                <div class="tab-content hidden" data-tab="4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label mb-3">Fotos da Vistoria</label>

                            @php $fotosExistentes = $vistoria->getMedia('fotos'); @endphp
                            @if($fotosExistentes->count() > 0)
                                <div class="mb-4">
                                    <p class="text-muted mb-2" style="font-size: var(--text-xs);">Fotos existentes:</p>
                                    <div class="photos-grid" id="fotos-existentes">
                                        @foreach($fotosExistentes as $foto)
                                            <div class="photo-preview" id="foto-existente-{{ $foto->id }}">
                                                <img src="{{ $foto->getUrl('thumb') }}" alt="Foto">
                                                <button type="button" onclick="marcarRemoverFoto({{ $foto->id }})" class="photo-remove-btn">
                                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

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
                                <span id="foto-count">0</span> nova(s) foto(s) selecionada(s)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Aba 6: Revisar e Finalizar -->
                <div class="tab-content hidden" data-tab="5">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="form-section-title">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Revisao da Vistoria
                            </h3>
                            <p class="text-muted mb-4" style="font-size: var(--text-sm);">Verifique os dados antes de salvar.</p>

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
                                Salvar Alteracoes
                            </button>
                            <a href="{{ route('vistorias.show', $vistoria) }}" class="btn btn-ghost btn-block mt-2">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inputs para fotos a remover -->
            <div id="fotos-remover-inputs"></div>
        </form>
    </div>

    <script>
        let currentTab = 0;
        const totalTabs = 6;
        let visitedSteps = new Set([0]);
        let recognition = null;
        let activeInput = null;
        let fotosSelecionadas = [];
        let fotosParaRemover = [];
        const tiposAbrigo = @json($tiposAbrigo);
        const abrigosTiposSelecionados = @json($vistoria->abrigos_tipos ?? []);

        const stepLabels = ['Dados', 'Caract.', 'Relatorio', 'Encam.', 'Fotos', 'Revisar'];
        const checkmarkSVG = '<svg class="stepper-check" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';

        document.addEventListener('DOMContentLoaded', function() {
            showTab(0);
            if (parseInt(document.getElementById('qtd_abrigos').value) > 0) {
                atualizarCamposAbrigos();
            }
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

            document.querySelector('.form-content')?.scrollTo({ top: 0, behavior: 'smooth' });

            // Ao entrar na aba de revisao, montar checklist
            if (index === totalTabs - 1) {
                buildReviewChecklist();
            }
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
                    label: 'Observacoes preenchidas',
                    step: 2,
                    check: () => !!document.querySelector('[name="observacoes"]')?.value?.trim(),
                    optional: true
                },
                {
                    label: 'Novas fotos anexadas',
                    step: 4,
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
                statusEl.innerHTML = '<div class="alert alert-success" style="margin:0;"><strong>Tudo certo!</strong> A vistoria esta pronta para ser salva.</div>';
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('btn-disabled');
            } else {
                statusEl.innerHTML = '<div class="alert alert-danger" style="margin:0;"><strong>Campos obrigatorios pendentes.</strong> Corrija antes de salvar.</div>';
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
                    const selectedValue = abrigosTiposSelecionados[i] || '';
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-2';
                    div.innerHTML = `
                        <span class="text-muted" style="width: 24px;">${i + 1}.</span>
                        <select name="abrigos_tipos[]" class="form-input form-select flex-1">
                            <option value="">Selecione...</option>
                            ${tiposAbrigo.map(t => `<option value="${t.id}" ${t.id == selectedValue ? 'selected' : ''}>${t.tipo_abrigo}</option>`).join('')}
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

        function marcarRemoverFoto(fotoId) {
            if (confirm('Remover esta foto?')) {
                fotosParaRemover.push(fotoId);
                document.getElementById('foto-existente-' + fotoId).style.display = 'none';
                atualizarInputsRemocao();
            }
        }

        function atualizarInputsRemocao() {
            const container = document.getElementById('fotos-remover-inputs');
            container.innerHTML = '';
            fotosParaRemover.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remover_fotos[]';
                input.value = id;
                container.appendChild(input);
            });
        }

        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
                   (navigator.maxTouchPoints && navigator.maxTouchPoints > 2 && /MacIntel/.test(navigator.platform));
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
            openCameraInput(type);
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
    </script>
@endsection
