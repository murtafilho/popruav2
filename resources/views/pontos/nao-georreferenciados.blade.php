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
                        <th class="hide-mobile">Bairro</th>
                        <th class="hide-mobile">Regional</th>
                        <th class="hide-mobile text-center">Vistorias</th>
                        <th>Resultado</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pontos as $ponto)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: var(--space-2);">
                                    <svg style="width: 16px; height: 16px; color: var(--status-warning); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Sem coordenadas">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span style="font-weight: var(--font-medium);">
                                        @if($ponto->logradouro)
                                            {{ $ponto->tipo }} {{ $ponto->logradouro }}, {{ $ponto->numero }}
                                            @if($ponto->complemento)
                                                <span class="text-muted">- {{ $ponto->complemento }}</span>
                                            @endif
                                        @elseif($ponto->complemento)
                                            {{ $ponto->complemento }}
                                            @if($ponto->numero && $ponto->numero !== 's/n')
                                                <span class="text-muted">- No {{ $ponto->numero }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Endereco nao cadastrado</span>
                                            @if($ponto->numero)
                                                <span class="text-muted">- No {{ $ponto->numero }}</span>
                                            @endif
                                        @endif
                                    </span>
                                </div>
                                @if($ponto->logradouro)
                                <button
                                    onclick="buscarCoordenadas({{ $ponto->id }}, '{{ addslashes($ponto->tipo ?? '') }}', '{{ addslashes($ponto->logradouro ?? '') }}', '{{ $ponto->numero ?? '' }}', '{{ addslashes($ponto->bairro ?? '') }}')"
                                    class="btn btn-primary btn-sm mt-2"
                                    id="btn-geocode-{{ $ponto->id }}"
                                >
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="btn-text">Buscar no mapa</span>
                                </button>
                                @endif
                                {{-- Mobile info --}}
                                <div class="mobile-only text-muted mt-1" style="font-size: var(--text-xs);">
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
                            <td>
                                <span class="badge badge-warning">
                                    <svg style="width: 12px; height: 12px; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Sem coords
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding: var(--space-6);">
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

// ESC fecha resultados
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        logradouroResults.classList.add('hidden');
    }
});

// ========== GEOCODIFICACAO VIA API EXTERNA ==========
async function buscarCoordenadas(pontoId, tipo, logradouro, numero, bairro) {
    const btn = document.getElementById(`btn-geocode-${pontoId}`);
    const btnText = btn.querySelector('.btn-text');
    const originalText = btnText.textContent;

    // Desabilita o botao e mostra loading
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
            // Redireciona para o mapa com as coordenadas encontradas
            window.location.href = `/mapa?lat=${data.lat}&lng=${data.lng}&zoom=18&ponto_id=${pontoId}&geocoded=1`;
        } else {
            alert('Nao foi possivel encontrar as coordenadas para este endereco.\n\n' + (data.message || 'Endereco nao encontrado no OpenStreetMap.'));
            // Restaura o botao
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
            btnText.textContent = originalText;
        }
    } catch (error) {
        console.error('Erro ao buscar coordenadas:', error);
        alert('Erro ao buscar coordenadas. Tente novamente.');
        // Restaura o botao
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        btnText.textContent = originalText;
    }
}
</script>
@endpush
