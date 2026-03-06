@extends('layouts.app')

@section('title', 'Pontos Nao Georreferenciados')

@section('header')
    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-icon" style="margin-left: -8px;">
        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <span class="mobile-header-title" style="flex: 1; text-align: center;">{{ __('Sem Georref.') }}</span>
    <div style="width: 44px;"></div>
@endsection

@section('content')
    <div class="page-content">
        {{-- Mensagem de sucesso --}}
        @if(request('success'))
            <div class="alert alert-success mb-4">
                <div class="alert-icon">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <p class="alert-title">Coordenadas salvas com sucesso!</p>
                    @if(request('ponto'))
                        <div class="alert-details">
                            <p><strong>Ponto:</strong> {{ request('ponto') }}@if(request('bairro')) - {{ request('bairro') }}@endif</p>
                            @if(request('referencia'))
                                <p><strong>Referencia:</strong> {{ request('referencia') }}</p>
                            @endif
                            @if(request('lat') && request('lng'))
                                <p class="text-muted" style="font-size: var(--text-xs);">
                                    Coordenadas: {{ number_format((float) request('lat'), 6) }}, {{ number_format((float) request('lng'), 6) }}
                                </p>
                            @endif
                        </div>
                    @endif
                    <a href="{{ route('mapa.index', ['lat' => request('lat'), 'lng' => request('lng'), 'zoom' => 18, 'endereco' => request('ponto'), 'referencia' => request('referencia')]) }}"
                       class="alert-link">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Ver no mapa
                    </a>
                </div>
            </div>
        @endif

        {{-- Alerta informativo --}}
        <div class="alert alert-warning mb-4">
            <div class="alert-icon">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="alert-content">
                <p class="alert-title">Pontos sem coordenadas</p>
                <p class="alert-message">Estes pontos nao possuem coordenadas geograficas (lat/lng) e nao aparecem no mapa.</p>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('pontos.nao-georreferenciados') }}" style="display: flex; flex-direction: column; gap: var(--space-3);" id="form-filtros">
                    {{-- Busca por Endereco --}}
                    <div class="form-row form-row-4">
                        <div class="form-group" style="grid-column: span 3;">
                            <label class="form-label">Logradouro</label>
                            <div class="autocomplete-container">
                                <input type="text" name="logradouro" id="search-logradouro"
                                       placeholder="Digite para buscar..." autocomplete="off"
                                       class="form-input">
                                <div id="logradouro-results" class="autocomplete-results hidden"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Numero</label>
                            <input type="number" name="numero" id="search-numero"
                                   placeholder="No" autocomplete="off"
                                   class="form-input">
                        </div>
                    </div>

                    <div class="form-row form-row-3">
                        <div class="form-group">
                            <label class="form-label">Bairro</label>
                            <select name="bairro" class="form-input form-select">
                                <option value="">Todos</option>
                                @foreach($bairros as $bairro)
                                    <option value="{{ $bairro }}" {{ request('bairro') == $bairro ? 'selected' : '' }}>
                                        {{ $bairro }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Regional</label>
                            <select name="regional" class="form-input form-select">
                                <option value="">Todas</option>
                                @foreach($regionais as $regional)
                                    <option value="{{ $regional }}" {{ request('regional') == $regional ? 'selected' : '' }}>
                                        {{ $regional }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Resultado</label>
                            <select name="resultado" class="form-input form-select">
                                <option value="">Todos</option>
                                @foreach($resultados as $resultado)
                                    <option value="{{ $resultado->id }}" {{ request('resultado') == $resultado->id ? 'selected' : '' }}>
                                        {{ $resultado->resultado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrar
                        </button>
                        <a href="{{ route('pontos.nao-georreferenciados') }}" class="btn btn-secondary">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="table-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Endereco</th>
                        <th class="hide-mobile">Descricao</th>
                        <th class="hide-mobile">Bairro</th>
                        <th class="hide-mobile">Regional</th>
                        <th class="hide-mobile text-center">Vistorias</th>
                        <th>Resultado</th>
                        <th class="text-center">Georreferenciar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pontos as $ponto)
                        <tr id="row-ponto-{{ $ponto->id }}">
                            <td>
                                <a href="{{ route('pontos.show', $ponto->id) }}" style="display: flex; align-items: center; gap: var(--space-2); text-decoration: none; color: inherit;">
                                    <svg style="width: 16px; height: 16px; color: var(--status-warning); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Sem coordenadas">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span style="font-weight: var(--font-medium);">
                                        @if($ponto->logradouro)
                                            {{ $ponto->tipo }} {{ $ponto->logradouro }}, {{ $ponto->numero }}
                                        @elseif($ponto->numero)
                                            <span class="text-muted">No {{ $ponto->numero }}</span>
                                        @else
                                            <span class="text-muted">Endereco nao cadastrado</span>
                                        @endif
                                    </span>
                                </a>
                                {{-- Mobile info --}}
                                <div class="mobile-only text-muted mt-1" style="font-size: var(--text-xs);">
                                    @if($ponto->complemento)
                                        <div style="margin-bottom: 2px;">{{ $ponto->complemento }}</div>
                                    @endif
                                    @if($ponto->bairro || $ponto->regional)
                                        {{ $ponto->bairro ?? '-' }} - {{ $ponto->regional ?? '-' }}
                                    @else
                                        <span class="text-warning">Sem endereco vinculado</span>
                                    @endif
                                    @if($ponto->total_vistorias > 0)
                                        <span class="badge badge-info" style="margin-left: var(--space-2);">
                                            {{ $ponto->total_vistorias }} vistoria(s)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="hide-mobile">
                                @if($ponto->complemento)
                                    {{ $ponto->complemento }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="hide-mobile">{{ $ponto->bairro ?? '-' }}</td>
                            <td class="hide-mobile">{{ $ponto->regional ?? '-' }}</td>
                            <td class="hide-mobile text-center">
                                @if($ponto->total_vistorias > 0)
                                    <a href="{{ route('pontos.show', $ponto->id) }}">
                                        <span class="badge badge-info">{{ $ponto->total_vistorias }}</span>
                                    </a>
                                @else
                                    <span class="badge badge-default">0</span>
                                @endif
                            </td>
                            <td>
                                @if($ponto->resultado_acao)
                                    @php
                                        $resultadoBadge = match($ponto->resultado_acao_id) {
                                            1 => 'badge-danger',
                                            2 => 'badge-warning',
                                            3, 4 => 'badge-default',
                                            5 => 'badge-info',
                                            6 => 'badge-success',
                                            default => 'badge-accent',
                                        };
                                    @endphp
                                    <span class="badge {{ $resultadoBadge }}">{{ $ponto->resultado_acao }}</span>
                                @else
                                    <span class="badge badge-accent">Sem vistoria</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $enderecoManual = trim(($ponto->tipo ?? '') . ' ' . ($ponto->logradouro ?? ($ponto->complemento ?? 'Ponto ' . $ponto->id)));
                                    if ($ponto->numero) {
                                        $enderecoManual .= ', ' . $ponto->numero;
                                    }
                                @endphp
                                @if($ponto->logradouro)
                                <button
                                    onclick="buscarCoordenadas({{ $ponto->id }}, '{{ addslashes($ponto->tipo ?? '') }}', '{{ addslashes($ponto->logradouro ?? '') }}', '{{ $ponto->numero ?? '' }}', '{{ addslashes($ponto->bairro ?? '') }}')"
                                    class="btn btn-primary btn-sm"
                                    id="btn-geocode-{{ $ponto->id }}"
                                >
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="btn-text">Georreferenciar</span>
                                </button>
                                @else
                                <button
                                    onclick="abrirMapaManual({{ $ponto->id }}, '{{ addslashes($enderecoManual) }}')"
                                    class="btn btn-secondary btn-sm"
                                    id="btn-manual-{{ $ponto->id }}"
                                >
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="btn-text">Georreferenciar</span>
                                </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: var(--space-6);">
                                Nenhum ponto nao georreferenciado encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginacao --}}
        @if($pontos->hasPages())
            <div class="pagination-wrapper">
                {{ $pontos->links() }}
            </div>
        @endif
    </div>
@endsection

{{-- Modal de Georreferenciamento Inline --}}
<div id="geocode-modal" class="geocode-modal hidden">
    <div class="geocode-modal-backdrop"></div>
    <div class="geocode-modal-content card">
        <div class="card-body" style="padding: 0;">
            {{-- Header --}}
            <div style="padding: var(--space-3) var(--space-4); border-bottom: 1px solid var(--border-primary); display: flex; align-items: center; gap: var(--space-3);">
                <svg style="width: 24px; height: 24px; color: var(--color-warning); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div style="flex: 1; min-width: 0;">
                    <h3 id="geocode-modal-title" style="font-size: var(--text-sm); font-weight: var(--font-semibold); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></h3>
                    <p class="text-muted" style="font-size: var(--text-xs); margin: 2px 0 0;">Clique no mapa para ajustar a posicao</p>
                </div>
                <button id="geocode-modal-close" class="btn btn-ghost btn-icon" style="flex-shrink: 0;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Busca de endereco + Mini Mapa --}}
            <div style="position: relative;">
                <div class="geocode-modal-search-wrap">
                    <div class="modal-search-bar">
                        <svg class="modal-search-bar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            id="modal-search-input"
                            placeholder="Buscar endereco..."
                            autocomplete="off"
                        >
                        <button type="button" id="btn-modal-search-clear" class="modal-search-btn modal-search-btn-clear" title="Limpar" style="display: none;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        <button type="button" id="btn-modal-search-go" class="modal-search-btn modal-search-btn-go" title="Buscar">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                        <div id="modal-search-results" class="hidden" style="position: absolute; left: 0; right: 0; top: 100%;"></div>
                    </div>
                </div>
                <div id="geocode-minimap" style="height: 350px; width: 100%;"></div>
            </div>

            {{-- Footer com coordenadas e botoes --}}
            <div style="padding: var(--space-3) var(--space-4); border-top: 1px solid var(--border-primary);">
                <p id="geocode-modal-coords" class="text-mono text-muted" style="font-size: var(--text-xs); margin: 0 0 var(--space-3);"></p>
                <div style="display: flex; gap: var(--space-2);">
                    <button id="btn-confirmar-inline" class="btn btn-success" style="flex: 1;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmar
                    </button>
                    <button id="btn-cancelar-inline" class="btn btn-secondary">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .geocode-modal {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: var(--space-4);
    }
    .geocode-modal.hidden { display: none; }
    .geocode-modal-backdrop {
        position: absolute;
        inset: 0;
        background: var(--bg-overlay);
    }
    .geocode-modal-content {
        position: relative;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        border: 1px solid var(--border-primary);
    }
    #geocode-minimap {
        overflow: hidden;
    }
    .geocode-modal-search-wrap {
        position: relative;
        z-index: 1000;
        padding: var(--space-2) var(--space-3);
        border-bottom: 1px solid var(--border-primary);
        background: var(--bg-secondary);
    }
    .modal-search-bar {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--bg-base);
        border: 1px solid var(--border-primary);
        border-radius: var(--radius-md);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .modal-search-bar:focus-within {
        border-color: var(--accent-primary);
        box-shadow: 0 0 0 2px var(--accent-dim);
    }
    .modal-search-bar-icon {
        width: 15px;
        height: 15px;
        margin-left: 10px;
        color: var(--text-muted);
        flex-shrink: 0;
    }
    .modal-search-bar input {
        flex: 1;
        background: none;
        border: none;
        outline: none;
        color: var(--text-primary);
        font-family: var(--font-body);
        font-size: var(--text-sm);
        padding: 7px 0 7px 8px;
        min-width: 0;
    }
    .modal-search-bar input::placeholder {
        color: var(--text-muted);
        opacity: 0.7;
    }
    .modal-search-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        flex-shrink: 0;
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: background 0.15s, color 0.15s, transform 0.1s;
        margin: 2px;
    }
    .modal-search-btn svg {
        width: 14px;
        height: 14px;
    }
    .modal-search-btn-clear {
        background: none;
        color: var(--text-muted);
    }
    .modal-search-btn-clear:hover {
        background: var(--color-danger-dim);
        color: var(--color-danger);
    }
    .modal-search-btn-clear:active {
        transform: scale(0.9);
    }
    .modal-search-btn-go {
        background: var(--accent-dim);
        color: var(--accent-primary);
    }
    .modal-search-btn-go:hover {
        background: var(--accent-primary);
        color: var(--text-inverse);
    }
    .modal-search-btn-go:active {
        transform: scale(0.9);
    }
    #modal-search-results {
        max-height: 250px;
        overflow-y: auto;
        background: var(--bg-primary);
        border: 1px solid var(--border-primary);
        border-top: none;
        border-radius: 0 0 var(--radius-md) var(--radius-md);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
    }
    #modal-search-results .autocomplete-item {
        display: block;
        width: 100%;
        text-align: left;
        padding: var(--space-2) var(--space-3);
        border: none;
        background: none;
        color: inherit;
        cursor: pointer;
        border-bottom: 1px solid var(--border-primary);
        transition: background 0.1s;
    }
    #modal-search-results .autocomplete-item:last-child {
        border-bottom: none;
    }
    #modal-search-results .autocomplete-item:hover {
        background: var(--bg-tertiary);
    }
    @media (max-width: 640px) {
        .geocode-modal { padding: 0; align-items: flex-end; }
        .geocode-modal-content {
            max-width: 100%;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            max-height: 85vh;
        }
        #geocode-minimap { height: 300px !important; }
    }
</style>
@endpush

@push('scripts')
<script>
// ========== AUTOCOMPLETE DE LOGRADOUROS ==========
const searchLogradouro = document.getElementById('search-logradouro');
const searchNumero = document.getElementById('search-numero');
const logradouroResults = document.getElementById('logradouro-results');
const formFiltros = document.getElementById('form-filtros');
let searchTimeout = null;

// Debounce da busca de logradouros
searchLogradouro.addEventListener('input', function() {
    const termo = this.value.trim();

    if (searchTimeout) clearTimeout(searchTimeout);

    if (termo.length < 2) {
        logradouroResults.classList.add('hidden');
        return;
    }

    searchTimeout = setTimeout(() => buscarLogradouros(termo), 300);
});

// Fechar resultados ao clicar fora
document.addEventListener('click', function(e) {
    if (!searchLogradouro.contains(e.target) && !logradouroResults.contains(e.target)) {
        logradouroResults.classList.add('hidden');
    }
});

// Buscar logradouros na API (pontos nao georreferenciados)
async function buscarLogradouros(termo) {
    try {
        logradouroResults.innerHTML = `
            <div class="autocomplete-loading">
                <svg class="spinner" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        `;
        logradouroResults.classList.remove('hidden');

        const response = await fetch(`/api/pontos/nao-georreferenciados/logradouros?q=${encodeURIComponent(termo)}`);
        const logradouros = await response.json();

        if (logradouros.length === 0) {
            logradouroResults.innerHTML = `
                <div class="autocomplete-empty">
                    Nenhum logradouro encontrado
                </div>
            `;
        } else {
            logradouroResults.innerHTML = logradouros.map(log => `
                <button type="button" class="autocomplete-item" data-logradouro="${log.logradouro}">
                    <div class="autocomplete-item-title">${log.tipo} ${log.logradouro}</div>
                    <div class="autocomplete-item-subtitle">${log.regional}</div>
                </button>
            `).join('');

            // Adicionar event listeners aos resultados
            logradouroResults.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const logradouro = this.dataset.logradouro;

                    // Preenche o campo com o logradouro selecionado
                    searchLogradouro.value = logradouro;
                    logradouroResults.classList.add('hidden');

                    // Foca no campo de numero ou submete o formulario
                    searchNumero.focus();
                });
            });
        }
    } catch (err) {
        console.error('Erro na busca:', err);
        logradouroResults.innerHTML = `
            <div class="autocomplete-error">
                Erro ao buscar logradouros
            </div>
        `;
    }
}

// ESC fecha resultados e modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        logradouroResults.classList.add('hidden');
        fecharModalGeocode();
    }
});

// ========== GEOCODIFICACAO INLINE COM MINI-MAPA ==========
let miniMap = null;
let miniMapMarker = null;
let currentPontoId = null;
let currentCoords = null;

const modal = document.getElementById('geocode-modal');
const modalTitle = document.getElementById('geocode-modal-title');
const modalCoords = document.getElementById('geocode-modal-coords');

function abrirModalGeocode(pontoId, lat, lng, endereco, zoom) {
    currentPontoId = pontoId;
    currentCoords = { lat, lng };
    modalTitle.textContent = endereco;
    modalCoords.textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;

    const zoomLevel = zoom || 18;

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Limpar busca anterior
    modalSearchInput.value = '';
    modalSearchResults.classList.add('hidden');
    modalSelectedLogradouro = null;
    if (document.getElementById('btn-modal-search-clear')) {
        document.getElementById('btn-modal-search-clear').style.display = 'none';
    }

    // Inicializar ou recriar mini-mapa
    if (miniMap) {
        miniMap.remove();
        miniMap = null;
    }

    setTimeout(() => {
        miniMap = L.map('geocode-minimap', {
            zoomControl: true,
            attributionControl: false
        }).setView([lat, lng], zoomLevel);

        // Camada satelite
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 22,
            maxNativeZoom: 19
        }).addTo(miniMap);

        // Camada de labels
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 22,
            maxNativeZoom: 19
        }).addTo(miniMap);

        // Marcador inicial (amarelo) - so mostra se veio de geocode (zoom alto)
        if (zoomLevel >= 16) {
            setMiniMapMarker(lat, lng, true);
        }

        // Clique no mapa para reposicionar
        miniMap.on('click', function(e) {
            setMiniMapMarker(e.latlng.lat, e.latlng.lng, false);
        });

        miniMap.invalidateSize();
    }, 50);
}

function setMiniMapMarker(lat, lng, isInitial) {
    if (miniMapMarker) {
        miniMap.removeLayer(miniMapMarker);
    }

    const color = isInitial ? '#eab308' : '#10b981';

    miniMapMarker = L.circleMarker([lat, lng], {
        radius: 14,
        fillColor: color,
        color: '#fff',
        weight: 3,
        opacity: 1,
        fillOpacity: 1
    }).addTo(miniMap);

    currentCoords = { lat, lng };
    modalCoords.textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
}

function fecharModalGeocode() {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    currentPontoId = null;
    currentCoords = null;
    // Limpar busca
    modalSearchInput.value = '';
    modalSearchResults.classList.add('hidden');
    modalSelectedLogradouro = null;
}

// Fechar modal
document.getElementById('geocode-modal-close').addEventListener('click', fecharModalGeocode);
document.getElementById('btn-cancelar-inline').addEventListener('click', fecharModalGeocode);
document.querySelector('.geocode-modal-backdrop').addEventListener('click', fecharModalGeocode);

// ========== BUSCA DE ENDERECO NO MINI-MAPA ==========
const modalSearchInput = document.getElementById('modal-search-input');
const modalSearchResults = document.getElementById('modal-search-results');
let modalSearchTimeout = null;
let modalSelectedLogradouro = null;

function parseModalEnderecoInput(valor) {
    const trimmed = valor.trim();
    const matchVirgula = trimmed.match(/^(.+?),\s*(\d+)\s*(?:-.*)?$/);
    if (matchVirgula) {
        return { texto: matchVirgula[1].trim(), numero: parseInt(matchVirgula[2]) };
    }
    const matchVirgulaSemNum = trimmed.match(/^(.+?),\s*(?:-.*)?$/);
    if (matchVirgulaSemNum) {
        return { texto: matchVirgulaSemNum[1].trim(), numero: null };
    }
    const match = trimmed.match(/^(.+?)[\s]+(\d+)\s*$/);
    if (match) {
        return { texto: match[1].trim(), numero: parseInt(match[2]) };
    }
    return { texto: trimmed, numero: null };
}

function extrairLogradouroPuro(texto) {
    return texto.replace(/^(AVE|RUA|PCA|ALA|TRV|BEC|PRC|VIA|ROD|EST|LAD)\s+/i, '');
}

// Impedir que cliques no input/resultados propaguem para o mapa
const btnModalSearchClear = document.getElementById('btn-modal-search-clear');
const btnModalSearchGo = document.getElementById('btn-modal-search-go');
modalSearchInput.addEventListener('mousedown', e => e.stopPropagation());
modalSearchResults.addEventListener('mousedown', e => e.stopPropagation());
modalSearchResults.addEventListener('click', e => e.stopPropagation());
btnModalSearchClear.addEventListener('mousedown', e => e.stopPropagation());
btnModalSearchGo.addEventListener('mousedown', e => e.stopPropagation());

// Mostrar/esconder botao limpar conforme o input tem texto
function toggleClearBtn() {
    btnModalSearchClear.style.display = modalSearchInput.value.length > 0 ? '' : 'none';
}

// Botao limpar
btnModalSearchClear.addEventListener('click', function(e) {
    e.stopPropagation();
    modalSearchInput.value = '';
    modalSearchResults.classList.add('hidden');
    modalSelectedLogradouro = null;
    toggleClearBtn();
    modalSearchInput.focus();
});

// Botao buscar
btnModalSearchGo.addEventListener('click', function(e) {
    e.stopPropagation();
    modalSearchResults.classList.add('hidden');
    buscarEnderecoModal();
});

modalSearchInput.addEventListener('input', function(e) {
    e.stopPropagation();
    toggleClearBtn();
    if (modalSearchTimeout) clearTimeout(modalSearchTimeout);

    if (modalSelectedLogradouro) {
        const prefixo = `${modalSelectedLogradouro.tipo} ${modalSelectedLogradouro.logradouro},`;
        if (!this.value.startsWith(prefixo)) {
            modalSelectedLogradouro = null;
        } else {
            modalSearchResults.classList.add('hidden');
            return;
        }
    }

    const { texto, numero } = parseModalEnderecoInput(this.value);
    if (texto.length < 2) {
        modalSearchResults.classList.add('hidden');
        return;
    }

    modalSearchTimeout = setTimeout(() => buscarLogradourosModal(texto, numero), 300);
});

modalSearchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        modalSearchResults.classList.add('hidden');
        buscarEnderecoModal();
    }
    e.stopPropagation();
});

// Fechar resultados ao clicar fora
document.addEventListener('click', function(e) {
    if (!modalSearchInput.contains(e.target) && !modalSearchResults.contains(e.target)) {
        modalSearchResults.classList.add('hidden');
    }
});

async function buscarLogradourosModal(termo, numero) {
    try {
        modalSearchResults.innerHTML = '<div style="padding: var(--space-2); text-align: center; color: var(--text-muted);">Buscando...</div>';
        modalSearchResults.classList.remove('hidden');

        const termoBusca = extrairLogradouroPuro(termo);
        let url = `/api/enderecos/logradouros?q=${encodeURIComponent(termoBusca)}`;
        if (numero) url += `&numero=${numero}`;

        const response = await fetch(url);
        const resultados = await response.json();

        if (resultados.length === 0) {
            modalSearchResults.innerHTML = '<div style="padding: var(--space-2); text-align: center; color: var(--text-muted);">Nenhum logradouro encontrado</div>';
        } else {
            modalSearchResults.innerHTML = resultados.map(item => {
                const tipo = item.tipo || '';
                const logr = item.logradouro || '';
                const reg = item.regional || '';
                const num = item.numero || '';
                const label = num ? `${tipo} ${logr}, ${num} - ${reg}` : `${tipo} ${logr} - ${reg}`;

                return `<button type="button" class="autocomplete-item" data-tipo="${tipo}" data-logradouro="${logr}" data-regional="${reg}" data-numero="${num}">
                    <div style="font-weight: var(--font-medium);">${label}</div>
                </button>`;
            }).join('');

            modalSearchResults.querySelectorAll('.autocomplete-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tipo = this.dataset.tipo;
                    const logradouro = this.dataset.logradouro;
                    const regional = this.dataset.regional;
                    const num = this.dataset.numero;
                    modalSelectedLogradouro = { tipo, logradouro, regional };

                    if (num) {
                        modalSearchInput.value = `${tipo} ${logradouro}, ${num} - ${regional}`;
                        modalSearchResults.classList.add('hidden');
                        buscarEnderecoModal();
                    } else {
                        modalSearchInput.value = `${tipo} ${logradouro}, `;
                        modalSearchResults.classList.add('hidden');
                        modalSearchInput.focus();
                    }
                });
            });
        }
    } catch (err) {
        console.error('Erro na busca:', err);
        modalSearchResults.innerHTML = '<div style="padding: var(--space-2); text-align: center; color: var(--color-danger);">Erro ao buscar</div>';
    }
}

async function buscarEnderecoModal() {
    const { texto, numero } = parseModalEnderecoInput(modalSearchInput.value);
    if (!texto) return;

    const logradouro = modalSelectedLogradouro
        ? modalSelectedLogradouro.logradouro
        : extrairLogradouroPuro(texto);

    try {
        const params = new URLSearchParams({ logradouro });
        if (numero && numero > 0) params.append('numero', numero);
        if (modalSelectedLogradouro?.regional) params.append('regional', modalSelectedLogradouro.regional);

        const response = await fetch(`/api/enderecos/buscar?${params}`);
        const result = await response.json();

        if (result.encontrado && miniMap) {
            const end = result.endereco;
            const lat = parseFloat(end.lat);
            const lng = parseFloat(end.lng);
            const numLabel = Math.round(end.numero);

            modalSearchInput.value = `${end.tipo} ${end.logradouro}, ${numLabel} - ${end.regional}`;

            miniMap.setView([lat, lng], 18);
            setMiniMapMarker(lat, lng, true);
        } else {
            alert('Endereco nao encontrado na base.');
        }
    } catch (err) {
        console.error('Erro na busca:', err);
        alert('Erro ao buscar endereco.');
    }
}

// Confirmar coordenadas
document.getElementById('btn-confirmar-inline').addEventListener('click', async function() {
    if (!currentCoords || !currentPontoId) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<svg class="spinner" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24"><circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';

    try {
        const response = await fetch(`/api/pontos/${currentPontoId}/coordenadas`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                lat: currentCoords.lat,
                lng: currentCoords.lng
            })
        });

        const data = await response.json();

        if (data.success) {
            fecharModalGeocode();

            // Atualizar a linha na tabela: remover o ponto georreferenciado
            const row = document.getElementById(`row-ponto-${currentPontoId}`);
            if (row) {
                row.style.transition = 'opacity 0.3s, background-color 0.3s';
                row.style.backgroundColor = 'var(--color-success-dim)';
                row.style.opacity = '0.6';
                // Substituir o badge de status
                const statusCell = row.querySelector('td:last-child');
                if (statusCell) {
                    statusCell.innerHTML = '<span class="badge badge-success"><svg style="width: 12px; height: 12px; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Salvo</span>';
                }
                // Remover apos 2s
                setTimeout(() => {
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                }, 2000);
            }

            // Restaurar botao
            btn.disabled = false;
            btn.innerHTML = '<svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Confirmar';
        } else {
            alert('Erro ao salvar coordenadas: ' + (data.message || 'Erro desconhecido'));
            btn.disabled = false;
            btn.innerHTML = '<svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Confirmar';
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao salvar coordenadas. Tente novamente.');
        btn.disabled = false;
        btn.innerHTML = '<svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Confirmar';
    }
});

// ========== POSICIONAMENTO MANUAL (SEM GEOCODE) ==========
const BH_CENTER = [-19.9167, -43.9345];

function abrirMapaManual(pontoId, endereco) {
    abrirModalGeocode(pontoId, BH_CENTER[0], BH_CENTER[1], endereco, 13);
}

// ========== GEOCODIFICACAO VIA API ==========
async function buscarCoordenadas(pontoId, tipo, logradouro, numero, bairro) {
    const btn = document.getElementById(`btn-geocode-${pontoId}`);
    const btnText = btn.querySelector('.btn-text');
    const originalText = btnText.textContent;

    btn.disabled = true;
    btn.style.opacity = '0.5';
    btn.style.cursor = 'not-allowed';
    btnText.textContent = 'Buscando...';

    try {
        const response = await fetch('/api/geocode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                logradouro: `${tipo} ${logradouro}`.trim(),
                numero: numero,
                bairro: bairro,
                cidade: 'Belo Horizonte'
            })
        });

        const data = await response.json();

        if (data.success) {
            // Abrir modal inline com mini-mapa
            const endereco = `${tipo} ${logradouro}, ${numero}`.trim();
            abrirModalGeocode(pontoId, data.lat, data.lng, endereco);
        } else {
            alert('Nao foi possivel encontrar as coordenadas para este endereco.\n\n' + (data.message || 'Endereco nao encontrado no OpenStreetMap.'));
        }

        // Restaura o botao
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        btnText.textContent = originalText;
    } catch (error) {
        console.error('Erro ao buscar coordenadas:', error);
        alert('Erro ao buscar coordenadas. Tente novamente.');
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        btnText.textContent = originalText;
    }
}
</script>
@endpush
