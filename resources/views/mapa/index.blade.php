@extends('layouts.app')

@section('title', 'Mapa')

@push('styles')
<style>
    .bairro-label,
    .regional-label {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    .bairro-label span,
    .regional-label span {
        display: inline-block;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }
    /* Indicador de Zoom no Footer */
    .footer-zoom-indicator {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        font-size: var(--text-xs);
        cursor: default;
    }
    .footer-zoom-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .footer-zoom-icon-danger {
        background-color: var(--color-danger);
        color: white;
    }
    .footer-zoom-icon-success {
        background-color: var(--color-success);
        color: white;
    }
    .footer-zoom-indicator span:last-child {
        color: var(--text-secondary);
    }
    .footer-zoom-indicator span:last-child strong {
        color: var(--text-primary);
    }
    /* Badge de Zoom no Bottom Sheet */
    .sheet-zoom-badge {
        display: inline-block;
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-full);
        font-weight: var(--font-semibold);
        font-size: var(--text-sm);
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .sheet-zoom-danger {
        background-color: var(--color-danger);
        color: white;
    }
    .sheet-zoom-success {
        background-color: var(--color-success);
        color: white;
    }
</style>
@endpush

@section('header')
    <div style="display: flex; align-items: center; gap: var(--space-2); flex: 1;">
        <!-- Campos de Busca -->
        <div class="map-search-bar">
            <!-- Logradouro -->
            <div class="map-search-field">
                <input
                    type="text"
                    id="search-logradouro"
                    placeholder="Logradouro"
                    autocomplete="off"
                    class="form-input form-input-sm"
                >
                <div id="logradouro-results" class="map-search-results hidden"></div>
            </div>
            <!-- Numero -->
            <div class="map-search-numero">
                <input
                    type="number"
                    id="search-numero"
                    placeholder="No"
                    autocomplete="off"
                    class="form-input form-input-sm"
                >
            </div>
            <!-- Botao Buscar -->
            <button
                type="button"
                id="btn-buscar-endereco"
                class="btn btn-ghost btn-icon btn-sm"
                title="Buscar endereco"
            >
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: var(--space-2);">
        <!-- Badge de Zoom -->
        <span id="zoom-badge" class="badge badge-danger" title="Zoom atual">
            <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
            </svg>
            <span id="zoom-level">12</span>
            <span id="zoom-warning" style="font-weight: bold; margin-left: 4px;">Zoom insuficiente</span>
        </span>
        <button id="btn-menu" class="btn btn-ghost btn-icon">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
@endsection

@section('content')
    <!-- Map Container -->
    <div id="map"></div>

    <!-- FAB - Minha Localizacao -->
    <button
        id="btn-my-location"
        class="map-fab"
        title="Minha localizacao"
    >
        <svg id="location-icon" style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <!-- Google Maps style crosshair -->
            <circle cx="12" cy="12" r="3" stroke-width="2"/>
            <circle cx="12" cy="12" r="7" stroke-width="2"/>
            <path stroke-linecap="round" stroke-width="2" d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
        </svg>
        <svg id="location-loader" style="width: 24px; height: 24px;" class="hidden spinner" fill="none" viewBox="0 0 24 24">
            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </button>

    <!-- Layers Panel (abre pelo menu) -->
    <div id="layers-panel" class="map-layers-panel hidden">
        <h4 class="layers-panel-title">Mapa Base</h4>
        <div class="layers-panel-group">
            <label class="layers-panel-option">
                <input type="radio" name="base-layer" id="base-street" class="form-checkbox">
                <span>Ruas</span>
            </label>
            <label class="layers-panel-option">
                <input type="radio" name="base-layer" id="base-satellite" class="form-checkbox" checked>
                <span>Satelite</span>
            </label>
        </div>

        <h4 class="layers-panel-title layers-panel-divider">Camadas</h4>
        <div class="layers-panel-group">
            <label class="layers-panel-option">
                <input type="checkbox" id="layer-regionais" class="form-checkbox">
                <span>Regionais</span>
            </label>
            <label class="layers-panel-option">
                <input type="checkbox" id="layer-bairros" class="form-checkbox">
                <span>Bairros</span>
            </label>
            <label class="layers-panel-option">
                <input type="checkbox" id="layer-limite" class="form-checkbox" checked>
                <span>Limite Municipal</span>
            </label>
            <label class="layers-panel-option">
                <input type="checkbox" id="layer-pontos" class="form-checkbox" checked>
                <span>Pontos</span>
            </label>
        </div>

        <h4 class="layers-panel-title layers-panel-divider">Filtrar por Resultado</h4>
        <div class="layers-panel-filters">
            <label class="layers-panel-filter">
                <input type="checkbox" data-resultado="1" class="filter-resultado form-checkbox" checked>
                <span class="filter-color" style="background-color: #dc2626;"></span>
                <span>Fenomeno persiste</span>
            </label>
            <label class="layers-panel-filter">
                <input type="checkbox" data-resultado="2" class="filter-resultado form-checkbox" checked>
                <span class="filter-color" style="background-color: #f97316;"></span>
                <span>Impactado parcialmente</span>
            </label>
            <label class="layers-panel-filter">
                <input type="checkbox" data-resultado="3" class="filter-resultado form-checkbox">
                <span class="filter-color" style="background-color: #1f2937;"></span>
                <span>Deixou de Ocorrer</span>
            </label>
            <label class="layers-panel-filter">
                <input type="checkbox" data-resultado="4" class="filter-resultado form-checkbox" checked>
                <span class="filter-color" style="background-color: #6b7280;"></span>
                <span>PSR ausente</span>
            </label>
            <label class="layers-panel-filter">
                <input type="checkbox" data-resultado="5" class="filter-resultado form-checkbox">
                <span class="filter-color" style="background-color: #3b82f6;"></span>
                <span>Nao constatado</span>
            </label>
            <label class="layers-panel-filter">
                <input type="checkbox" data-resultado="6" class="filter-resultado form-checkbox" checked>
                <span class="filter-color" style="background-color: #10b981;"></span>
                <span>Em Conformidade</span>
            </label>
            <label class="layers-panel-filter">
                <input type="checkbox" data-resultado="null" class="filter-resultado form-checkbox" checked>
                <span class="filter-color" style="background-color: #a855f7;"></span>
                <span>Sem vistoria</span>
            </label>
        </div>
    </div>

    <!-- Bottom Sheet - Point Info -->
    <div id="bottom-sheet" class="bottom-sheet">
        <div class="bottom-sheet-content">
            <div class="bottom-sheet-handle"></div>
            <div id="sheet-content">
                <!-- Content will be dynamically inserted -->
            </div>
        </div>
    </div>

    <!-- Modal Relatorio -->
    <div id="relatorio-modal" class="relatorio-modal hidden">
        <div class="relatorio-modal-overlay" onclick="fecharRelatorio()"></div>
        <div class="relatorio-modal-content">
            <div class="relatorio-modal-header">
                <span class="relatorio-modal-title">Relatorio da Vistoria</span>
                <button type="button" class="relatorio-modal-close" onclick="fecharRelatorio()">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="relatorio-modal-body">
                <div id="relatorio-loader" class="relatorio-loader">
                    <div class="loading-spinner"></div>
                </div>
                <iframe id="relatorio-iframe" src="" frameborder="0"></iframe>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <div class="footer-nav">
        <button class="footer-nav-item active">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span>Mapa</span>
        </button>
        <button id="btn-vistoria" class="footer-nav-item">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Nova Vistoria</span>
        </button>
        <!-- Indicador de Zoom no Footer -->
        <div id="footer-zoom-indicator" class="footer-nav-item footer-zoom-indicator" title="Zoom minimo para vistoria: 19">
            <span id="footer-zoom-icon" class="footer-zoom-icon footer-zoom-icon-danger">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                </svg>
            </span>
            <span>Zoom: <strong id="footer-zoom-level">12</strong></span>
        </div>
        <a href="{{ route('dashboard') }}" class="footer-nav-item">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Home</span>
        </a>
    </div>
@endsection

@push('scripts')
<script>
        const APP_BASE = "{{ rtrim(url('/'), '/') }}";
document.addEventListener('DOMContentLoaded', function() {
    // Belo Horizonte center coordinates (média dos pontos no banco)
    const BH_CENTER = [-19.9135, -43.9514];
    const DEFAULT_ZOOM = 12;
    const MIN_ZOOM_VISTORIA = 19;

    // Verificar se há parâmetros de localização na URL
    const urlParams = new URLSearchParams(window.location.search);
    const lat = urlParams.get('lat');
    const lng = urlParams.get('lng');
    const zoom = urlParams.get('zoom') ? parseInt(urlParams.get('zoom')) : DEFAULT_ZOOM;
    const pontoId = urlParams.get('ponto_id');
    const geocoded = urlParams.get('geocoded') === '1';
    const enderecoParam = urlParams.get('endereco');
    const referenciaParam = urlParams.get('referencia');

    // Initialize map
    const map = L.map('map', {
        zoomControl: false
    });

    // Add zoom control to bottom-left (better for mobile)
    L.control.zoom({ position: 'bottomleft' }).addTo(map);

    // Atualizar indicador de zoom com badge colorido (header e footer)
    const zoomLevel = document.getElementById('zoom-level');
    const zoomBadge = document.getElementById('zoom-badge');
    const zoomWarning = document.getElementById('zoom-warning');
    const footerZoomLevel = document.getElementById('footer-zoom-level');
    const footerZoomIcon = document.getElementById('footer-zoom-icon');

    function updateZoomBadge() {
        const currentZoom = map.getZoom();

        // Atualiza header
        zoomLevel.textContent = currentZoom;

        // Atualiza footer
        if (footerZoomLevel) {
            footerZoomLevel.textContent = currentZoom;
        }

        // Atualiza bottom sheet (se estiver aberto com aviso de zoom)
        const sheetZoomLevel = document.getElementById('sheet-zoom-level');
        const sheetZoomStatus = document.getElementById('sheet-zoom-status');
        const sheetZoomTitle = document.getElementById('sheet-zoom-title');
        const sheetZoomActions = document.getElementById('sheet-zoom-actions');

        if (sheetZoomLevel) {
            sheetZoomLevel.textContent = currentZoom;

            // Atualiza o badge e mensagem quando zoom atinge o mínimo
            if (currentZoom >= MIN_ZOOM_VISTORIA && sheetZoomStatus) {
                sheetZoomStatus.classList.remove('sheet-zoom-danger');
                sheetZoomStatus.classList.add('sheet-zoom-success');
                sheetZoomStatus.textContent = 'Zoom OK';

                if (sheetZoomTitle) {
                    sheetZoomTitle.textContent = 'Pronto para vistoria!';
                }

                // Atualiza botões para mostrar opção de criar vistoria
                if (sheetZoomActions && window.pendingVistoriaCoords) {
                    const coords = window.pendingVistoriaCoords;
                    sheetZoomActions.innerHTML = `
                        <button onclick="hideBottomSheet()" class="btn btn-secondary" style="flex: 1;">Fechar</button>
                        <a href="${APP_BASE}/vistorias/create?lat=${coords.lat}&lng=${coords.lng}" class="btn btn-success" style="flex: 1;">
                            <svg style="width: 16px; height: 16px; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nova Vistoria
                        </a>
                    `;
                }
            } else if (currentZoom < MIN_ZOOM_VISTORIA && sheetZoomStatus) {
                sheetZoomStatus.classList.remove('sheet-zoom-success');
                sheetZoomStatus.classList.add('sheet-zoom-danger');
                sheetZoomStatus.textContent = 'Zoom insuficiente';

                if (sheetZoomTitle) {
                    sheetZoomTitle.textContent = 'Aproxime mais o mapa';
                }

                // Restaura botões originais
                if (sheetZoomActions && window.pendingVistoriaCoords) {
                    const coords = window.pendingVistoriaCoords;
                    sheetZoomActions.innerHTML = `
                        <button onclick="hideBottomSheet()" class="btn btn-secondary" style="flex: 1;">Fechar</button>
                        <button onclick="window.zoomAndCloseSheet(${coords.lat}, ${coords.lng})" class="btn btn-primary" style="flex: 1;">Aproximar</button>
                    `;
                }
            }
        }

        // Muda cor: vermelho com aviso até 18, verde de 19 em diante
        if (currentZoom >= MIN_ZOOM_VISTORIA) {
            zoomBadge.classList.remove('badge-danger');
            zoomBadge.classList.add('badge-success');
            zoomWarning.style.display = 'none';

            // Footer: verde quando zoom suficiente
            if (footerZoomIcon) {
                footerZoomIcon.classList.remove('footer-zoom-icon-danger');
                footerZoomIcon.classList.add('footer-zoom-icon-success');
            }
        } else {
            zoomBadge.classList.remove('badge-success');
            zoomBadge.classList.add('badge-danger');
            zoomWarning.style.display = 'inline';

            // Footer: vermelho quando zoom insuficiente
            if (footerZoomIcon) {
                footerZoomIcon.classList.remove('footer-zoom-icon-success');
                footerZoomIcon.classList.add('footer-zoom-icon-danger');
            }
        }
    }

    updateZoomBadge();
    map.on('zoomend', updateZoomBadge);

    // Camadas base
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '&copy; Esri &mdash; Sources: Esri, DigitalGlobe, GeoEye, Earthstar Geographics',
        maxZoom: 19
    });

    // Adiciona camada de satélite por padrão
    satelliteLayer.addTo(map);
    let currentBaseLayer = satelliteLayer;

    // Variável para armazenar marcador do ponto selecionado
    let selectedPointMarker = null;
    let geocodeMarker = null; // Marcador para modo geocodificação
    let geocodeMode = geocoded && pontoId; // Modo de geocodificação ativo
    let currentGeocodeCoords = null; // Coordenadas atuais para geocodificação

    // Função para atualizar coordenadas exibidas
    function updateGeocodeCoords(lat, lng) {
        currentGeocodeCoords = { lat, lng };
        const coordsEl = document.getElementById('geocode-coords');
        if (coordsEl) {
            coordsEl.textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
        }
    }

    // Função para criar/mover marcador de geocodificação
    function setGeocodeMarker(lat, lng, isInitial = false) {
        // Remove marcador anterior se existir
        if (geocodeMarker) {
            map.removeLayer(geocodeMarker);
        }

        // Cor amarela para marcador inicial, verde para reposicionado
        const markerColor = isInitial ? '#eab308' : '#10b981';

        geocodeMarker = L.circleMarker([lat, lng], {
            radius: 14,
            fillColor: markerColor,
            color: '#fff',
            weight: 3,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map);

        updateGeocodeCoords(lat, lng);
    }

    // Se há coordenadas na URL, usar elas; senão usar o padrão
    if (lat && lng) {
        const pointLat = parseFloat(lat);
        const pointLng = parseFloat(lng);
        map.setView([pointLat, pointLng], zoom);

        // Adicionar marcador no ponto após o mapa carregar
        map.whenReady(() => {
            if (geocodeMode) {
                // Modo geocodificação: mostrar marcador amarelo
                setGeocodeMarker(pointLat, pointLng, true);
            } else {
                // Modo normal: marcador azul
                selectedPointMarker = L.circleMarker([pointLat, pointLng], {
                    radius: 12,
                    fillColor: '#3b82f6',
                    color: '#fff',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 1
                }).addTo(map);

                // Montar popup com informações do endereço
                let popupContent = '<div style="font-size: var(--text-sm);">';
                if (enderecoParam) {
                    popupContent += `<p style="font-weight: var(--font-semibold); color: var(--text-primary);">${decodeURIComponent(enderecoParam)}</p>`;
                }
                if (referenciaParam) {
                    popupContent += `<p style="font-size: var(--text-xs); color: var(--text-secondary); margin-top: var(--space-1);"><strong>Ref:</strong> ${decodeURIComponent(referenciaParam)}</p>`;
                }
                popupContent += `<p class="text-mono" style="font-size: var(--text-xs); color: var(--text-muted); margin-top: var(--space-1);">${pointLat.toFixed(6)}, ${pointLng.toFixed(6)}</p>`;
                popupContent += '</div>';

                selectedPointMarker.bindPopup(popupContent).openPopup();
            }
        });
    } else {
        map.setView(BH_CENTER, DEFAULT_ZOOM);
    }

    // Handler para clique no mapa em modo geocodificação
    if (geocodeMode) {
        map.on('click', function(e) {
            setGeocodeMarker(e.latlng.lat, e.latlng.lng, false);
        });

        // Handler para botão de confirmação
        document.getElementById('btn-confirmar-geocode').addEventListener('click', async function() {
            if (!currentGeocodeCoords) return;

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<svg class="spinner" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24"><circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';

            try {
                const response = await fetch(`/api/pontos/${pontoId}/coordenadas`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        lat: currentGeocodeCoords.lat,
                        lng: currentGeocodeCoords.lng
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Sucesso - redireciona para lista com mensagem e detalhes
                    const params = new URLSearchParams({
                        success: '1',
                        ponto: data.endereco_ponto || '',
                        bairro: data.bairro || '',
                        referencia: data.endereco_referencia || '',
                        lat: data.lat,
                        lng: data.lng
                    });
                    window.location.href = '/pontos/nao-georreferenciados?' + params.toString();
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
    }

    // Flag para evitar conflito entre clique em marcador e clique no mapa
    let markerClickedRecently = false;

    // Layers - usando MarkerClusterGroup para melhor performance
    const markersLayer = L.markerClusterGroup({
        chunkedLoading: true,
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        disableClusteringAtZoom: 18
    }).addTo(map);

    let regionaisLayer = null;
    let regionaisLabelsLayer = null;
    let bairrosLayer = null;
    let bairrosLabelsLayer = null;
    let limiteLayer = null;
    let allPointsLoaded = false;
    let allMarkers = []; // Armazena todos os markers para filtrar

    // Layer styles
    const styles = {
        regionais: {
            color: '#3b82f6',
            weight: 2,
            opacity: 0.8,
            fillOpacity: 0.1
        },
        bairros: {
            color: '#22c55e',
            weight: 1,
            opacity: 0.6,
            fillOpacity: 0.05
        },
        limite: {
            color: '#ef4444',
            weight: 3,
            opacity: 1,
            fillOpacity: 0
        }
    };

    // Cores por resultado da ação (última vistoria)
    const coresResultado = {
        1: '#dc2626', // Fenômeno persiste - Vermelho
        2: '#f97316', // Impactado parcialmente - Laranja
        3: '#1f2937', // Deixou de Ocorrer - Preto
        4: '#6b7280', // PSR ausente - Cinza
        5: '#3b82f6', // Fenômeno não constatado - Azul
        6: '#10b981', // Em Conformidade - Verde escuro
        null: '#a855f7' // Sem vistoria - Roxo
    };

    const legendaResultado = {
        1: 'Fenômeno persiste',
        2: 'Impactado parcialmente',
        3: 'Deixou de Ocorrer',
        4: 'PSR ausente',
        5: 'Não constatado',
        6: 'Em Conformidade',
        null: 'Sem vistoria'
    };

    // Load GeoJSON layers
    async function loadRegionais() {
        if (regionaisLayer) return;
        const response = await fetch('/api/geo/regionais');
        const data = await response.json();
        regionaisLayer = L.geoJSON(data, {
            style: styles.regionais,
            onEachFeature: (feature, layer) => {
                layer.bindTooltip(feature.properties.nome, {
                    permanent: false,
                    direction: 'center',
                    className: 'map-tooltip'
                });
            }
        });

        // Criar layer de labels das regionais
        regionaisLabelsLayer = L.layerGroup();
        data.features.forEach(feature => {
            if (feature.properties && feature.properties.nome) {
                // Calcular centroide do polígono
                const bounds = L.geoJSON(feature).getBounds();
                const center = bounds.getCenter();

                const label = L.marker(center, {
                    icon: L.divIcon({
                        className: 'regional-label',
                        html: `<span class="map-label map-label-regional">${feature.properties.nome}</span>`,
                        iconSize: null,
                        iconAnchor: [0, 0]
                    })
                });
                regionaisLabelsLayer.addLayer(label);
            }
        });
    }

    // Controlar visibilidade das labels de regionais baseado no zoom
    function updateRegionaisLabels() {
        if (!regionaisLabelsLayer || !document.getElementById('layer-regionais').checked) return;

        const zoom = map.getZoom();
        if (zoom >= 12 && zoom < 15) {
            if (!map.hasLayer(regionaisLabelsLayer)) {
                regionaisLabelsLayer.addTo(map);
            }
        } else {
            if (map.hasLayer(regionaisLabelsLayer)) {
                map.removeLayer(regionaisLabelsLayer);
            }
        }
    }

    async function loadBairros() {
        if (bairrosLayer) return;
        const response = await fetch('/api/geo/bairros');
        const data = await response.json();
        bairrosLayer = L.geoJSON(data, {
            style: styles.bairros,
            onEachFeature: (feature, layer) => {
                layer.bindTooltip(feature.properties.nome, {
                    permanent: false,
                    direction: 'center',
                    className: 'map-tooltip map-tooltip-sm'
                });
            }
        });

        // Criar layer de labels dos bairros
        bairrosLabelsLayer = L.layerGroup();
        data.features.forEach(feature => {
            if (feature.properties && feature.properties.nome) {
                // Calcular centroide do polígono
                const bounds = L.geoJSON(feature).getBounds();
                const center = bounds.getCenter();

                const label = L.marker(center, {
                    icon: L.divIcon({
                        className: 'bairro-label',
                        html: `<span class="map-label map-label-bairro">${feature.properties.nome}</span>`,
                        iconSize: null,
                        iconAnchor: [0, 0]
                    })
                });
                bairrosLabelsLayer.addLayer(label);
            }
        });
    }

    // Controlar visibilidade das labels de bairros baseado no zoom
    function updateBairrosLabels() {
        if (!bairrosLabelsLayer || !document.getElementById('layer-bairros').checked) return;

        const zoom = map.getZoom();
        if (zoom >= 15) {
            if (!map.hasLayer(bairrosLabelsLayer)) {
                bairrosLabelsLayer.addTo(map);
            }
        } else {
            if (map.hasLayer(bairrosLabelsLayer)) {
                map.removeLayer(bairrosLabelsLayer);
            }
        }
    }

    // Listener de zoom para labels
    map.on('zoomend', function() {
        updateRegionaisLabels();
        updateBairrosLabels();
    });

    async function loadLimite() {
        if (limiteLayer) return;
        const response = await fetch('/api/geo/limite-municipio');
        const data = await response.json();
        limiteLayer = L.geoJSON(data, {
            style: styles.limite
        });
    }

    // Load all points once (clusters will handle display)
    function loadAllPoints() {
        if (allPointsLoaded || !document.getElementById('layer-pontos').checked) return;

        console.log('Carregando todos os pontos...');

        // Buscar todos os pontos de BH (bounds amplos)
        const params = new URLSearchParams({
            north: -19.7,
            south: -20.1,
            east: -43.8,
            west: -44.1
        });

        fetch(`/api/pontos?${params}`)
            .then(response => response.json())
            .then(data => {
                console.log('Pontos recebidos:', data.length);

                allMarkers = [];
                data.forEach(ponto => {
                    if (ponto.lat && ponto.lng) {
                        const lat = parseFloat(ponto.lat);
                        const lng = parseFloat(ponto.lng);
                        const resultadoId = ponto.resultado_acao_id;
                        const cor = coresResultado[resultadoId] || coresResultado[null];
                        const status = legendaResultado[resultadoId] || legendaResultado[null];

                        const totalVistorias = ponto.total_vistorias || 0;
                        const complexidade = ponto.complexidade || 0;
                        const ultimaVistoriaId = ponto.ultima_vistoria_id;
                        const relatorioLink = ultimaVistoriaId
                            ? `<br><a href="#" onclick="event.stopPropagation(); abrirRelatorio(${ultimaVistoriaId}); return false;" style="font-size: 11px; color: #3b82f6; text-decoration: underline;">Ver relatorio</a>`
                            : '';
                        const complexidadeCor = complexidade >= 8 ? '#dc2626' : complexidade >= 4 ? '#f59e0b' : '#6b7280';
                        const marker = L.circleMarker([lat, lng], {
                            radius: 8,
                            fillColor: cor,
                            color: '#fff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.9,
                            bubblingMouseEvents: false
                        }).bindPopup(`
                                <strong>${ponto.logradouro}, ${ponto.numero}</strong><br>
                                <small>${ponto.bairro} - ${ponto.regional}</small><br>
                                <span style="color:${cor}; font-weight:bold;">● ${status}</span><br>
                                <span style="font-size: 11px; color: #6b7280;">Vistorias: <strong>${totalVistorias}</strong> | Complexidade: <strong style="color:${complexidadeCor}">${complexidade}</strong></span>${relatorioLink}
                            `);
                        marker.pontoData = ponto;

                        // Handler de clique individual (garante funcionamento com bubblingMouseEvents: false)
                        marker.on('click', function(e) {
                            L.DomEvent.stopPropagation(e);
                            markerClickedRecently = true;
                            setTimeout(() => { markerClickedRecently = false; }, 300);
                            showPointDetails(ponto);
                        });
                        marker.resultadoId = resultadoId; // Armazena para filtro
                        allMarkers.push(marker);
                    }
                });

                applyFilters();
                allPointsLoaded = true;
                console.log('Markers carregados:', allMarkers.length);
            })
            .catch(err => console.error('Erro ao carregar pontos:', err));
    }

    // Função para aplicar filtros
    function applyFilters() {
        const activeFilters = new Set();
        document.querySelectorAll('.filter-resultado:checked').forEach(cb => {
            const val = cb.dataset.resultado;
            activeFilters.add(val === 'null' ? null : parseInt(val));
        });

        markersLayer.clearLayers();
        const filteredMarkers = allMarkers.filter(marker => activeFilters.has(marker.resultadoId));
        markersLayer.addLayers(filteredMarkers);
        console.log('Markers visíveis:', filteredMarkers.length);
    }

    // Event listeners para filtros
    document.querySelectorAll('.filter-resultado').forEach(cb => {
        cb.addEventListener('change', applyFilters);
    });

    // Load points once on startup
    loadAllPoints();

    // Load limite layer by default (checked)
    loadLimite().then(() => {
        if (document.getElementById('layer-limite').checked && limiteLayer) {
            limiteLayer.addTo(map);
        }
    });

    // Layer controls - abre pelo menu do header
    const layersPanel = document.getElementById('layers-panel');

    document.getElementById('btn-menu').addEventListener('click', () => {
        layersPanel.classList.toggle('hidden');
    });

    // Alternância de camada base
    document.getElementById('base-street').addEventListener('change', function() {
        if (this.checked) {
            map.removeLayer(currentBaseLayer);
            streetLayer.addTo(map);
            currentBaseLayer = streetLayer;
        }
    });

    document.getElementById('base-satellite').addEventListener('change', function() {
        if (this.checked) {
            map.removeLayer(currentBaseLayer);
            satelliteLayer.addTo(map);
            currentBaseLayer = satelliteLayer;
        }
    });

    document.getElementById('layer-regionais').addEventListener('change', async function() {
        if (this.checked) {
            await loadRegionais();
            regionaisLayer.addTo(map);
            updateRegionaisLabels(); // Mostrar labels se zoom >= 12 e < 15
        } else {
            if (regionaisLayer) {
                map.removeLayer(regionaisLayer);
            }
            if (regionaisLabelsLayer) {
                map.removeLayer(regionaisLabelsLayer);
            }
        }
    });

    document.getElementById('layer-bairros').addEventListener('change', async function() {
        if (this.checked) {
            await loadBairros();
            bairrosLayer.addTo(map);
            updateBairrosLabels(); // Mostrar labels se zoom >= 15
        } else {
            if (bairrosLayer) {
                map.removeLayer(bairrosLayer);
            }
            if (bairrosLabelsLayer) {
                map.removeLayer(bairrosLabelsLayer);
            }
        }
    });

    document.getElementById('layer-limite').addEventListener('change', async function() {
        if (this.checked) {
            await loadLimite();
            limiteLayer.addTo(map);
        } else if (limiteLayer) {
            map.removeLayer(limiteLayer);
        }
    });

    document.getElementById('layer-pontos').addEventListener('change', function() {
        if (this.checked) {
            markersLayer.addTo(map);
            if (!allPointsLoaded) {
                loadAllPoints();
            }
        } else {
            map.removeLayer(markersLayer);
        }
    });

    // Bottom sheet functions
    const bottomSheet = document.getElementById('bottom-sheet');
    const sheetContent = document.getElementById('sheet-content');

    function showBottomSheet(content) {
        sheetContent.innerHTML = content;
        bottomSheet.classList.remove('translate-y-full');
    }

    function hideBottomSheet() {
        bottomSheet.classList.add('translate-y-full');
    }

    async function showPointDetails(ponto) {
        // Limpa marcador azul de seleção se existir
        clearUserLocation();

        // Mostra loading
        showBottomSheet(`
            <div class="sheet-loading">
                <div class="loading-spinner"></div>
            </div>
        `);

        // Busca detalhes completos do ponto
        try {
            const response = await fetch(`/api/pontos/${ponto.id}`);
            const details = await response.json();

            showBottomSheet(`
                <h3 class="sheet-title">${details.logradouro}, ${details.numero}</h3>
                <p class="sheet-subtitle">${details.bairro} - ${details.regional}</p>
                <div class="sheet-stats">
                    <div class="sheet-stat">
                        <div class="sheet-stat-value">${details.contador || 0}</div>
                        <div class="sheet-stat-label">Vistorias</div>
                    </div>
                    <div class="sheet-stat">
                        <div class="sheet-stat-value">${details.soma_kg || 0}</div>
                        <div class="sheet-stat-label">Kg</div>
                    </div>
                    <div class="sheet-stat">
                        <div class="sheet-stat-value">${details.complexidade || 0}</div>
                        <div class="sheet-stat-label">Complex.</div>
                    </div>
                </div>
                <button onclick="window.location.href='${APP_BASE}/pontos/${details.id}/vistorias/create'" class="btn btn-primary btn-block">
                    Nova Vistoria
                </button>
            `);
        } catch (err) {
            showBottomSheet(`
                <p class="sheet-error">Erro ao carregar detalhes</p>
            `);
        }
    }

    // Evento de clique no layer de marcadores (mais confiável que eventos individuais)
    markersLayer.on('click', function(e) {
        const marker = e.layer;
        if (marker && marker.pontoData) {
            markerClickedRecently = true;
            setTimeout(() => { markerClickedRecently = false; }, 300);
            showPointDetails(marker.pontoData);
        }
    });

    // Geolocation - botão FAB
    let userLocationMarker = null;
    let accuracyCircle = null;
    let locationMode = false; // Modo de seleção de localização ativo
    let currentLocation = null; // Armazena lat/lng atual

    const btnVistoria = document.getElementById('btn-vistoria');

    function enableVistoriaButton() {
        btnVistoria.disabled = false;
        btnVistoria.classList.add('active');
    }

    // Limpa o marcador azul de seleção de localização
    function clearUserLocation() {
        if (userLocationMarker) {
            map.removeLayer(userLocationMarker);
            userLocationMarker = null;
        }
        if (accuracyCircle) {
            map.removeLayer(accuracyCircle);
            accuracyCircle = null;
        }
        currentLocation = null;
        locationMode = false;
    }

    function setUserLocation(lat, lng, accuracy = null) {
        currentLocation = { lat, lng };
        // Remove marcadores anteriores
        if (userLocationMarker) {
            map.removeLayer(userLocationMarker);
        }
        if (accuracyCircle) {
            map.removeLayer(accuracyCircle);
        }

        // Adiciona marcador de localização (arrastável)
        userLocationMarker = L.circleMarker([lat, lng], {
            radius: 12,
            fillColor: '#3b82f6',
            color: '#fff',
            weight: 3,
            opacity: 1,
            fillOpacity: 1,
            draggable: true
        }).addTo(map);

        // Atualiza localização quando o marcador é arrastado
        userLocationMarker.on('dragend', function(e) {
            const newLat = e.target.getLatLng().lat;
            const newLng = e.target.getLatLng().lng;
            currentLocation = { lat: newLat, lng: newLng };
            
            // Remove círculo de precisão ao arrastar (não é mais preciso)
            if (accuracyCircle) {
                map.removeLayer(accuracyCircle);
                accuracyCircle = null;
            }
        });

        // Adiciona círculo de precisão se disponível (apenas na primeira vez, não ao arrastar)
        if (accuracy && !accuracyCircle) {
            accuracyCircle = L.circle([lat, lng], {
                radius: accuracy,
                color: '#3b82f6',
                fillColor: '#3b82f6',
                fillOpacity: 0.1,
                weight: 1
            }).addTo(map);
        }

        // Habilita botão de vistoria
        enableVistoriaButton();
    }

    document.getElementById('btn-my-location').addEventListener('click', function() {
        const btn = this;
        const icon = document.getElementById('location-icon');
        const loader = document.getElementById('location-loader');
        
        // Função para restaurar estado normal
        const restoreButton = () => {
            if (loader) loader.classList.add('hidden');
            if (icon) icon.classList.remove('hidden');
            btn.classList.remove('active');
            btn.style.pointerEvents = 'auto';
        };
        
        // Mostra loader e esconde ícone
        if (icon) icon.classList.add('hidden');
        if (loader) loader.classList.remove('hidden');
        btn.classList.add('active');
        btn.style.pointerEvents = 'none'; // Desabilita cliques temporariamente

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    setUserLocation(lat, lng, position.coords.accuracy);
                    map.setView([lat, lng], MIN_ZOOM_VISTORIA);

                    locationMode = true; // Ativa modo de seleção
                    
                    // Restaura botão com cor primária
                    restoreButton();
                    btn.classList.add('active');
                },
                error => {
                    // Restaura botão ao estado normal
                    restoreButton();
                    alert('Não foi possível obter sua localização. Verifique as permissões do navegador.');
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            // Restaura botão ao estado normal
            restoreButton();
            alert('Geolocalização não suportada neste navegador.');
        }
    });

    // Armazena dados do endereço atual para uso no botão Vistoria
    let currentEndereco = null;

    // Clique no mapa cria ponto provisório, busca endereço e habilita vistoria
    map.on('click', async function(e) {
        // Ignora se clicou em um marcador recentemente
        if (markerClickedRecently) {
            return;
        }

        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Verifica zoom mínimo para criar vistoria
        if (map.getZoom() < MIN_ZOOM_VISTORIA) {
            showBottomSheet(`
                <div class="sheet-address-info">
                    <p id="sheet-zoom-status" class="sheet-label sheet-zoom-badge sheet-zoom-danger">Zoom insuficiente</p>
                    <h3 id="sheet-zoom-title" class="sheet-title">Aproxime mais o mapa</h3>
                    <p class="sheet-subtitle">Zoom atual: <strong id="sheet-zoom-level">${map.getZoom()}</strong> | Minimo necessario: ${MIN_ZOOM_VISTORIA}</p>
                </div>
                <div id="sheet-zoom-actions" class="sheet-actions">
                    <button onclick="hideBottomSheet()" class="btn btn-secondary" style="flex: 1;">Fechar</button>
                    <button onclick="window.zoomAndCloseSheet(${lat}, ${lng})" class="btn btn-primary" style="flex: 1;">Aproximar</button>
                </div>
            `);
            // Armazenar coordenadas para uso quando zoom atingir o mínimo
            window.pendingVistoriaCoords = { lat, lng };
            return;
        }

        // Cria/move o marcador provisório
        setUserLocation(lat, lng);

        // Ativa o modo de seleção
        locationMode = true;

        // Busca o endereço de porta mais próximo
        try {
            const response = await fetch(`/api/enderecos/por-coordenadas?lat=${lat}&lng=${lng}`);
            const data = await response.json();

            if (data.encontrado) {
                const end = data.endereco;
                const enderecoLabel = `${end.tipo} ${end.logradouro}, ${Math.round(end.numero)}`;
                const distancia = data.distancia_metros;

                // Armazenar endereço para uso no botão Vistoria
                currentEndereco = end;

                // Montar URL com dados do endereço
                const vistoriaParams = new URLSearchParams({
                    lat: lat,
                    lng: lng,
                    endereco_tipo: end.tipo || '',
                    endereco_logradouro: end.logradouro || '',
                    endereco_numero: Math.round(end.numero) || '',
                    endereco_bairro: end.bairro || '',
                    endereco_regional: end.regional || '',
                    endereco_distancia: distancia
                });

                showBottomSheet(`
                    <div class="sheet-address-info">
                        <p class="sheet-label">Endereco de porta mais proximo (${distancia}m)</p>
                        <h3 class="sheet-title">${enderecoLabel}</h3>
                        <p class="sheet-subtitle">${end.bairro} - ${end.regional}</p>
                    </div>
                    <div class="sheet-actions">
                        <button onclick="hideBottomSheet()" class="btn btn-secondary" style="flex: 1;">Cancelar</button>
                        <a href="${APP_BASE}/vistorias/create?${vistoriaParams.toString()}" class="btn btn-primary" style="flex: 1;"><svg style="width: 16px; height: 16px; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Nova Vistoria Aqui</a>
                    </div>
                `);
            } else {
                // Mesmo sem endereço próximo, permitir criar vistoria
                currentEndereco = null;
                const vistoriaParams = new URLSearchParams({
                    lat: lat,
                    lng: lng
                });

                showBottomSheet(`
                    <div class="sheet-address-info">
                        <p class="sheet-label">Nenhum endereco de porta proximo</p>
                        <h3 class="sheet-title">Local sem referencia</h3>
                        <p class="sheet-subtitle">Coordenadas: ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                    </div>
                    <div class="sheet-actions">
                        <button onclick="hideBottomSheet()" class="btn btn-secondary" style="flex: 1;">Cancelar</button>
                        <a href="${APP_BASE}/vistorias/create?${vistoriaParams.toString()}" class="btn btn-primary" style="flex: 1;"><svg style="width: 16px; height: 16px; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Nova Vistoria Aqui</a>
                    </div>
                `);
            }
        } catch (err) {
            console.error('Erro ao buscar endereço:', err);
            // Mesmo com erro, permitir criar vistoria apenas com coordenadas
            currentEndereco = null;
            const vistoriaParams = new URLSearchParams({
                lat: lat,
                lng: lng
            });

            showBottomSheet(`
                <div class="sheet-address-info">
                    <p class="sheet-label">Erro ao buscar endereco</p>
                    <h3 class="sheet-title">Local sem referencia</h3>
                    <p class="sheet-subtitle">Coordenadas: ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                </div>
                <div class="sheet-actions">
                    <button onclick="hideBottomSheet()" class="btn btn-secondary" style="flex: 1;">Cancelar</button>
                    <a href="${APP_BASE}/vistorias/create?${vistoriaParams.toString()}" class="btn btn-primary" style="flex: 1;"><svg style="width: 16px; height: 16px; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Nova Vistoria Aqui</a>
                </div>
            `);
        }
    });

    // Botão de vistoria
    btnVistoria.addEventListener('click', async function() {
        // Se nao tem ponto selecionado, mostrar popup informativo
        if (!currentLocation) {
            showBottomSheet(`
                <div class="sheet-warning">
                    <div class="sheet-warning-icon">
                        <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="sheet-title">Selecione um Ponto</h3>
                    <p class="sheet-text">Para registrar uma nova vistoria, voce precisa:</p>
                    <div class="sheet-instructions">
                        <div class="sheet-instruction">
                            <span class="sheet-instruction-number">1.</span>
                            <span>Clicar no mapa para criar um <strong>novo ponto</strong></span>
                        </div>
                        <div class="sheet-instruction">
                            <span class="sheet-instruction-number">2.</span>
                            <span>Ou selecionar um <strong>ponto existente</strong> no mapa</span>
                        </div>
                    </div>
                    <button onclick="hideBottomSheet()" class="btn btn-primary btn-block">Entendi</button>
                </div>
            `);
            return;
        }

        // Mostrar loading
        showBottomSheet(`
            <div class="sheet-loading">
                <div class="loading-spinner"></div>
            </div>
        `);

        // Buscar endereço se não tiver
        if (!currentEndereco) {
            try {
                const response = await fetch(`/api/enderecos/por-coordenadas?lat=${currentLocation.lat}&lng=${currentLocation.lng}`);
                const data = await response.json();
                if (data.encontrado) {
                    currentEndereco = data.endereco;
                }
            } catch (err) {
                console.error('Erro ao buscar endereço:', err);
            }
        }

        // Montar URL com dados do endereço
        const vistoriaParams = new URLSearchParams({
            lat: currentLocation.lat,
            lng: currentLocation.lng
        });

        let enderecoHtml = '';
        if (currentEndereco) {
            const end = currentEndereco;
            vistoriaParams.set('endereco_tipo', end.tipo || '');
            vistoriaParams.set('endereco_logradouro', end.logradouro || '');
            vistoriaParams.set('endereco_numero', Math.round(end.numero) || '');
            vistoriaParams.set('endereco_bairro', end.bairro || '');
            vistoriaParams.set('endereco_regional', end.regional || '');

            enderecoHtml = `
                <p class="sheet-address">${end.tipo} ${end.logradouro}, ${Math.round(end.numero)}</p>
                <p class="sheet-subtitle">${end.bairro} - ${end.regional}</p>
            `;
        }

        showBottomSheet(`
            <h3 class="sheet-title">Nova Vistoria</h3>
            ${enderecoHtml}
            <p class="sheet-coords">${currentLocation.lat.toFixed(6)}, ${currentLocation.lng.toFixed(6)}</p>
            <div class="sheet-actions-vertical">
                <a href="${APP_BASE}/vistorias/create?${vistoriaParams.toString()}" class="btn btn-primary btn-block">Registrar Vistoria</a>
                <button onclick="hideBottomSheet()" class="btn btn-secondary btn-block">Cancelar</button>
            </div>
        `);
    });

    // Expor hideBottomSheet globalmente para o onclick inline
    window.hideBottomSheet = hideBottomSheet;

    // Função para aproximar zoom e fechar o bottom sheet
    window.zoomAndCloseSheet = function(lat, lng) {
        map.setView([lat, lng], MIN_ZOOM_VISTORIA);
        hideBottomSheet();
    };

    // Modal do Relatorio
    function abrirRelatorio(vistoriaId) {
        const modal = document.getElementById('relatorio-modal');
        const iframe = document.getElementById('relatorio-iframe');
        const loader = document.getElementById('relatorio-loader');

        modal.classList.remove('hidden');
        loader.classList.remove('hidden');
        iframe.classList.add('hidden');

        iframe.src = `/vistorias/${vistoriaId}/relatorio`;

        iframe.onload = function() {
            loader.classList.add('hidden');
            iframe.classList.remove('hidden');
        };

        document.body.style.overflow = 'hidden';
    }

    function fecharRelatorio() {
        const modal = document.getElementById('relatorio-modal');
        const iframe = document.getElementById('relatorio-iframe');

        modal.classList.add('hidden');
        iframe.src = '';
        document.body.style.overflow = '';
    }

    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharRelatorio();
        }
    });

    // Expor funcoes globalmente
    window.abrirRelatorio = abrirRelatorio;
    window.fecharRelatorio = fecharRelatorio;

    // ========== BUSCA DE ENDEREÇO ==========
    const searchLogradouro = document.getElementById('search-logradouro');
    const searchNumero = document.getElementById('search-numero');
    const logradouroResults = document.getElementById('logradouro-results');
    const btnBuscar = document.getElementById('btn-buscar-endereco');
    let searchMarker = null;
    let searchTimeout = null;
    let selectedLogradouro = null; // Armazena o logradouro selecionado (sem tipo)

    // Prevenir que eventos do mapa afetem os inputs
    if (searchLogradouro) {
        searchLogradouro.addEventListener('click', e => e.stopPropagation());
    }
    if (searchNumero) {
        searchNumero.addEventListener('click', e => e.stopPropagation());
    }

    // Autocomplete de logradouros
    if (searchLogradouro && logradouroResults) {
        searchLogradouro.addEventListener('input', function(e) {
            e.stopPropagation();
            const termo = this.value.trim();
            selectedLogradouro = null; // Limpa seleção ao digitar

            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            if (termo.length < 2) {
                logradouroResults.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                buscarLogradouros(termo);
            }, 300);
        });

        // Fechar ao clicar fora
        document.addEventListener('click', function(e) {
            if (!searchLogradouro.contains(e.target) && !logradouroResults.contains(e.target)) {
                logradouroResults.classList.add('hidden');
            }
        });
    }

    // Função para buscar logradouros
    async function buscarLogradouros(termo) {
        try {
            logradouroResults.innerHTML = '<div style="padding: var(--space-2); text-align: center;">Carregando...</div>';
            logradouroResults.classList.remove('hidden');

            const response = await fetch(`/api/enderecos/logradouros?q=${encodeURIComponent(termo)}`);
            const logradouros = await response.json();

            if (logradouros.length === 0) {
                logradouroResults.innerHTML = '<div style="padding: var(--space-2); text-align: center; color: var(--text-muted);">Nenhum logradouro encontrado</div>';
            } else {
                logradouroResults.innerHTML = logradouros.map(log => `
                    <button type="button" class="autocomplete-item" data-tipo="${log.tipo || ''}" data-logradouro="${log.logradouro || ''}" data-regional="${log.regional || ''}">
                        <div style="font-weight: var(--font-medium);">${log.tipo || ''} ${log.logradouro || ''}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">${log.regional || ''}</div>
                    </button>
                `).join('');

                // Adicionar event listeners
                logradouroResults.querySelectorAll('.autocomplete-item').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tipo = this.dataset.tipo;
                        const logradouro = this.dataset.logradouro;
                        selectedLogradouro = logradouro; // Armazena o logradouro puro para busca
                        searchLogradouro.value = `${tipo} ${logradouro}`;
                        logradouroResults.classList.add('hidden');
                        searchNumero.focus();
                    });
                });
            }
        } catch (err) {
            console.error('Erro na busca:', err);
            logradouroResults.innerHTML = '<div style="padding: var(--space-2); text-align: center; color: var(--color-danger);">Erro ao buscar logradouros</div>';
        }
    }

    // Buscar endereco completo (logradouro + numero opcional)
    async function buscarEnderecoCompleto() {
        // Usa o logradouro selecionado do autocomplete, ou extrai do campo digitado
        let logradouro = selectedLogradouro;
        if (!logradouro) {
            // Se digitou manualmente, tenta extrair removendo o tipo do início
            const valorCampo = searchLogradouro.value.trim();
            if (!valorCampo) {
                alert('Digite um logradouro');
                searchLogradouro.focus();
                return;
            }
            // Remove prefixos comuns de tipo (AVE, RUA, PCA, etc.)
            logradouro = valorCampo.replace(/^(AVE|RUA|PCA|ALA|TRV|BEC|PRC|VIA|ROD|EST|LAD)\s+/i, '');
        }

        const numeroInput = searchNumero.value.trim();
        const numero = numeroInput ? parseInt(numeroInput) : null;

        try {
            const params = new URLSearchParams({ logradouro: logradouro });
            if (numero && numero > 0) {
                params.append('numero', numero);
            }

            const response = await fetch(`/api/enderecos/buscar?${params}`);
            const result = await response.json();

            if (result.encontrado) {
                const end = result.endereco;
                const lat = parseFloat(end.lat);
                const lng = parseFloat(end.lng);

                // Determina o tipo de resultado
                const numeroInformado = result.numero_informado;
                const isAproximado = !result.exato || !numeroInformado;
                const enderecoLabel = `${end.tipo} ${end.logradouro}, ${Math.round(end.numero)}`;

                // Mensagem personalizada
                let mensagemAproximado = null;
                if (!numeroInformado) {
                    mensagemAproximado = 'centro';
                } else if (!result.exato) {
                    mensagemAproximado = numero;
                }

                irParaEndereco(lat, lng, enderecoLabel, end.bairro, end.regional, isAproximado, mensagemAproximado);
            } else {
                alert('Endereço não encontrado');
            }
        } catch (err) {
            console.error('Erro na busca:', err);
            alert('Erro ao buscar endereço');
        }
    }

    // Botão de buscar
    btnBuscar.addEventListener('click', buscarEnderecoCompleto);

    // Enter no campo de número dispara busca
    searchNumero.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarEnderecoCompleto();
        }
    });

    // Navegar para o endereço no mapa
    function irParaEndereco(lat, lng, endereco, bairro, regional, isAproximado = false, mensagemAproximado = null) {
        // Remove marcador anterior se existir
        if (searchMarker) {
            map.removeLayer(searchMarker);
        }

        // Adiciona marcador destacado (amarelo)
        searchMarker = L.circleMarker([lat, lng], {
            radius: 14,
            fillColor: '#f59e0b',
            color: '#fff',
            weight: 3,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map);

        // Centraliza e ajusta zoom
        map.setView([lat, lng], 18);

        // Mostra informacoes no bottom sheet
        let avisoAproximado = '';
        if (isAproximado && mensagemAproximado === 'centro') {
            avisoAproximado = '<div class="alert alert-info" style="margin-bottom: var(--space-3);">Mostrando o centro do logradouro.</div>';
        } else if (isAproximado && mensagemAproximado) {
            avisoAproximado = `<div class="alert alert-warning" style="margin-bottom: var(--space-3);">Numero ${mensagemAproximado} nao encontrado. Mostrando o mais proximo.</div>`;
        }

        showBottomSheet(`
            ${avisoAproximado}
            <h3 class="sheet-title">${endereco}</h3>
            <p class="sheet-subtitle" style="margin-bottom: var(--space-4);">${bairro} - ${regional}</p>
            <div class="sheet-actions">
                <button onclick="hideBottomSheet()" class="btn btn-secondary" style="flex: 1;">Cancelar</button>
                <a href="${APP_BASE}/vistorias/create?lat=${lat}&lng=${lng}" class="btn btn-primary" style="flex: 1;"><svg style="width: 16px; height: 16px; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Nova Vistoria Aqui</a>
            </div>
        `);

        // Armazena localização para uso no botão Vistoria
        currentLocation = { lat, lng };
        enableVistoriaButton();

        // Limpa campos
        searchLogradouro.value = '';
        searchNumero.value = '';
    }
});
</script>
@endpush
