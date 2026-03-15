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
    .map-search-field-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
        z-index: 2;
    }

</style>
@endpush

@section('header')
    <div style="display: flex; align-items: center; gap: var(--space-2); flex: 1;">
        <!-- Campo de Busca Unificado -->
        <div class="map-search-bar">
            <div class="map-search-field">
                <svg class="map-search-field-icon" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    id="search-endereco"
                    placeholder="Buscar endereco..."
                    autocomplete="off"
                    class="form-input form-input-sm"
                    style="padding-left: 32px;"
                >
                <div id="search-results" class="map-search-results hidden"></div>
            </div>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: var(--space-2);">
        <button id="btn-menu" class="btn btn-ghost btn-icon">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
@endsection

@section('content')
    <!-- Map Container -->
    <div id="map">
        <!-- Crosshair no centro do mapa (dentro do #map para alinhar com o centro do Leaflet) -->
        <div class="map-crosshair">
            <div class="map-crosshair-h"></div>
            <div class="map-crosshair-v"></div>
        </div>
    </div>

    <!-- Botão Nova Ação -->
    <button
        id="btn-nova-acao"
        class="map-btn-nova-acao"
        disabled
    >
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Ação
    </button>

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
        <div class="layers-panel-header">
            <h4 class="layers-panel-title">Camadas</h4>
            <button type="button" id="layers-panel-close" class="btn btn-ghost btn-icon" style="margin: -8px -8px -8px 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <h4 class="layers-panel-title layers-panel-divider">Mapa Base</h4>
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
    const ajustarMode = urlParams.get('ajustar') === '1' && pontoId;
    const enderecoParam = urlParams.get('endereco');
    const referenciaParam = urlParams.get('referencia');

    // Initialize map
    const map = L.map('map', {
        zoomControl: false,
        attributionControl: false
    });



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
            } else if (ajustarMode) {
                // Modo ajuste: marcador visível na posição original
                selectedPointMarker = L.circleMarker([pointLat, pointLng], {
                    radius: 14,
                    fillColor: '#f59e0b',
                    color: '#fff',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(map);

                // Esconde botão Nova Ação
                const btnNovaAcaoEl = document.getElementById('btn-nova-acao');
                if (btnNovaAcaoEl) btnNovaAcaoEl.style.display = 'none';

                // Buscar info do ponto e atualizar painel + popup
                fetch(`${APP_BASE}/api/pontos/${pontoId}`)
                    .then(r => r.json())
                    .then(data => {
                        const endEl = document.getElementById('ajustar-endereco');
                        const enderecoTexto = data.logradouro
                            ? `${data.tipo || ''} ${data.logradouro}, ${data.numero || 'S/N'}`
                            : 'Ponto #' + pontoId;
                        if (endEl) {
                            endEl.textContent = enderecoTexto;
                        }
                        // Popup no marcador (autoPan desabilitado para manter crosshair no ponto)
                        selectedPointMarker.bindPopup(
                            `<div style="font-size: var(--text-sm);">` +
                            `<p style="font-weight: var(--font-semibold); color: var(--text-primary);">${enderecoTexto}</p>` +
                            (data.bairro ? `<p style="font-size: var(--text-xs); color: var(--text-secondary); margin-top: 2px;">${data.bairro}${data.resultado ? ' — ' + data.resultado : ''}</p>` : '') +
                            `<p class="text-mono" style="font-size: var(--text-xs); color: var(--text-muted); margin-top: 4px;">${pointLat.toFixed(6)}, ${pointLng.toFixed(6)}</p>` +
                            `</div>`,
                            { autoPan: false }
                        ).openPopup();
                    })
                    .catch(() => {});

                // Atualiza coordenadas no painel conforme crosshair (centro do mapa)
                function updateAjustarCoords() {
                    const center = map.getCenter();
                    const coordsEl = document.getElementById('ajustar-coords');
                    if (coordsEl) {
                        coordsEl.textContent = `${center.lat.toFixed(6)}, ${center.lng.toFixed(6)}`;
                    }
                }
                updateAjustarCoords();
                map.on('moveend', updateAjustarCoords);

                // Botão confirmar ajuste
                const btnConfirmar = document.getElementById('btn-confirmar-ajuste');
                if (btnConfirmar) {
                    btnConfirmar.addEventListener('click', async function() {
                        const center = map.getCenter();
                        const btn = this;
                        btn.disabled = true;
                        btn.innerHTML = '<svg class="spinner" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24"><circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';

                        try {
                            const response = await fetch(`${APP_BASE}/api/pontos/${pontoId}/coordenadas`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ lat: center.lat, lng: center.lng })
                            });

                            const data = await response.json();

                            if (data.success) {
                                window.location.href = `${APP_BASE}/pontos?ajuste_sucesso=1&ponto_endereco=${encodeURIComponent(data.endereco_ponto || '')}`;
                            } else {
                                alert('Erro ao salvar: ' + (data.message || 'Erro desconhecido'));
                                btn.disabled = false;
                                btn.innerHTML = '<svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Salvar';
                            }
                        } catch (error) {
                            console.error('Erro:', error);
                            alert('Erro ao salvar coordenadas. Tente novamente.');
                            btn.disabled = false;
                            btn.innerHTML = '<svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Salvar';
                        }
                    });
                }
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

    // Listener de zoom para labels e tamanho dos marcadores
    map.on('zoomend', function() {
        updateRegionaisLabels();
        updateBairrosLabels();

        // Aumenta marcadores a partir do zoom 18
        const zoom = map.getZoom();
        const radius = zoom >= 18 ? 14 : 8;
        const weight = zoom >= 18 ? 3 : 2;
        allMarkers.forEach(m => {
            m.setRadius(radius);
            m.setStyle({ weight });
        });
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

                        // Handler de clique individual — centraliza no ponto
                        marker.on('click', function(e) {
                            L.DomEvent.stopPropagation(e);
                            markerClickedRecently = true;
                            setTimeout(() => { markerClickedRecently = false; }, 300);
                            map.flyTo(e.latlng, 18);
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

    // Load points once on startup (skip in ajustar mode to isolate the target ponto)
    if (!ajustarMode) {
        loadAllPoints();
    }

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

    document.getElementById('layers-panel-close').addEventListener('click', () => {
        layersPanel.classList.add('hidden');
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


    // Evento de clique no layer de marcadores — centraliza no ponto
    markersLayer.on('click', function(e) {
        const marker = e.layer;
        if (marker && marker.pontoData) {
            markerClickedRecently = true;
            setTimeout(() => { markerClickedRecently = false; }, 300);
            map.flyTo(e.latlng, 18);
        }
    });

    // Geolocation - botão FAB
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

                    map.flyTo([lat, lng], 18);

                    // Restaura botão
                    restoreButton();
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
    // Clique no mapa centraliza no ponto clicado com zoom 18 (desabilitado no modo ajuste)
    map.on('click', function(e) {
        if (markerClickedRecently || ajustarMode) return;
        map.flyTo(e.latlng, 18);
    });

    // Atualiza dados do endereço do crosshair (centro do mapa)
    let crosshairFetchController = null;
    async function updateCrosshairAddress() {
        const center = map.getCenter();
        const lat = center.lat;
        const lng = center.lng;
        const zoom = map.getZoom();

        if (zoom < 16) {
            currentCrosshairEndereco = null;
            return;
        }

        // Cancela fetch anterior se ainda pendente
        if (crosshairFetchController) crosshairFetchController.abort();
        crosshairFetchController = new AbortController();

        try {
            const response = await fetch(`${APP_BASE}/api/enderecos/por-coordenadas?lat=${lat}&lng=${lng}`, {
                signal: crosshairFetchController.signal
            });
            const data = await response.json();

            if (data.encontrado) {
                const end = data.endereco;
                const vistoriaParams = new URLSearchParams({
                    lat, lng,
                    endereco_tipo: end.tipo || '',
                    endereco_logradouro: end.logradouro || '',
                    endereco_numero: Math.round(end.numero) || '',
                    endereco_bairro: end.bairro || '',
                    endereco_regional: end.regional || '',
                    endereco_distancia: data.distancia_metros
                });
                currentCrosshairEndereco = { lat, lng, end, distancia: data.distancia_metros, vistoriaParams };
            } else {
                currentCrosshairEndereco = null;
            }
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('Erro ao buscar endereço:', err);
            }
        }
    }

    // Atualiza endereço do crosshair quando o mapa para de mover
    map.on('moveend', updateCrosshairAddress);

    // Dados do endereço atual do crosshair
    let currentCrosshairEndereco = null;

    // Ativa/desativa botão Nova Ação conforme zoom (>= 18)
    const btnNovaAcao = document.getElementById('btn-nova-acao');
    function updateBtnNovaAcao() {
        btnNovaAcao.disabled = map.getZoom() < 18;
    }
    map.on('zoomend', updateBtnNovaAcao);

    // Clique no botão Nova Ação — redireciona para criar vistoria com coords do crosshair
    btnNovaAcao.addEventListener('click', function() {
        if (currentCrosshairEndereco) {
            window.location.href = `${APP_BASE}/vistorias/create?${currentCrosshairEndereco.vistoriaParams.toString()}`;
        } else {
            const center = map.getCenter();
            const params = new URLSearchParams({ lat: center.lat, lng: center.lng });
            window.location.href = `${APP_BASE}/vistorias/create?${params.toString()}`;
        }
    });

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
    const searchInput = document.getElementById('search-endereco');
    const searchResults = document.getElementById('search-results');
    let searchMarker = null;
    let searchTimeout = null;
    let selectedLogradouro = null; // { tipo, logradouro, regional }

    // Prevenir que eventos do mapa afetem o input
    if (searchInput) {
        searchInput.addEventListener('click', e => e.stopPropagation());
    }

    // Extrai logradouro e número do texto digitado
    // Formato esperado: "AVE AFONSO PENA, 1000 - CENTRO" ou "AFONSO PENA 1000"
    function parseEnderecoInput(valor) {
        const trimmed = valor.trim();
        // Formato com vírgula: "TIPO LOGRADOURO, NUMERO - REGIONAL"
        const matchVirgula = trimmed.match(/^(.+?),\s*(\d+)\s*(?:-.*)?$/);
        if (matchVirgula) {
            return { texto: matchVirgula[1].trim(), numero: parseInt(matchVirgula[2]) };
        }
        // Formato com vírgula sem número: "TIPO LOGRADOURO, - REGIONAL" ou "TIPO LOGRADOURO,"
        const matchVirgulaSemNum = trimmed.match(/^(.+?),\s*(?:-.*)?$/);
        if (matchVirgulaSemNum) {
            return { texto: matchVirgulaSemNum[1].trim(), numero: null };
        }
        // Formato simples: "LOGRADOURO 1000"
        const match = trimmed.match(/^(.+?)[\s]+(\d+)\s*$/);
        if (match) {
            return { texto: match[1].trim(), numero: parseInt(match[2]) };
        }
        return { texto: trimmed, numero: null };
    }

    // Extrai logradouro puro removendo prefixo de tipo
    function extrairLogradouro(texto) {
        return texto.replace(/^(AVE|RUA|PCA|ALA|TRV|BEC|PRC|VIA|ROD|EST|LAD)\s+/i, '');
    }

    // Autocomplete
    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function(e) {
            e.stopPropagation();

            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Se já selecionou logradouro, só fecha o dropdown (usuário está digitando número)
            if (selectedLogradouro) {
                // Se apagou o texto do logradouro selecionado, reseta
                const prefixo = `${selectedLogradouro.tipo} ${selectedLogradouro.logradouro},`;
                if (!this.value.startsWith(prefixo)) {
                    selectedLogradouro = null;
                } else {
                    searchResults.classList.add('hidden');
                    return;
                }
            }

            const { texto, numero } = parseEnderecoInput(this.value);
            if (texto.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                buscarLogradouros(texto, numero);
            }, 300);
        });

        // Fechar ao clicar fora
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    }

    // Buscar logradouros para autocomplete
    async function buscarLogradouros(termo, numero = null) {
        try {
            searchResults.innerHTML = '<div style="padding: var(--space-2); text-align: center;">Buscando...</div>';
            searchResults.classList.remove('hidden');

            // Remove prefixo de tipo para busca
            const termoBusca = extrairLogradouro(termo);

            let url = `/api/enderecos/logradouros?q=${encodeURIComponent(termoBusca)}`;
            if (numero) {
                url += `&numero=${numero}`;
            }

            const response = await fetch(url);
            const resultados = await response.json();

            if (resultados.length === 0) {
                searchResults.innerHTML = '<div style="padding: var(--space-2); text-align: center; color: var(--text-muted);">Nenhum logradouro encontrado</div>';
            } else {
                searchResults.innerHTML = resultados.map(item => {
                    const tipo = item.tipo || '';
                    const logr = item.logradouro || '';
                    const reg = item.regional || '';
                    const num = item.numero || '';

                    const label = num
                        ? `${tipo} ${logr}, ${num} - ${reg}`
                        : `${tipo} ${logr} - ${reg}`;

                    return `
                        <button type="button" class="autocomplete-item" data-tipo="${tipo}" data-logradouro="${logr}" data-regional="${reg}" data-numero="${num}">
                            <div style="font-weight: var(--font-medium);">${label}</div>
                        </button>
                    `;
                }).join('');

                searchResults.querySelectorAll('.autocomplete-item').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tipo = this.dataset.tipo;
                        const logradouro = this.dataset.logradouro;
                        const regional = this.dataset.regional;
                        const num = this.dataset.numero;
                        selectedLogradouro = { tipo, logradouro, regional };

                        if (num) {
                            // Com número: preenche formato completo e busca direto
                            searchInput.value = `${tipo} ${logradouro}, ${num} - ${regional}`;
                            searchResults.classList.add('hidden');
                            buscarEnderecoCompleto();
                        } else {
                            // Sem número: preenche logradouro e espera número
                            searchInput.value = `${tipo} ${logradouro}, `;
                            searchResults.classList.add('hidden');
                            searchInput.focus();
                        }
                    });
                });
            }
        } catch (err) {
            console.error('Erro na busca:', err);
            searchResults.innerHTML = '<div style="padding: var(--space-2); text-align: center; color: var(--color-danger);">Erro ao buscar</div>';
        }
    }

    // Buscar endereço completo
    async function buscarEnderecoCompleto() {
        const { texto, numero } = parseEnderecoInput(searchInput.value);
        if (!texto) {
            searchInput.focus();
            return;
        }

        const logradouro = selectedLogradouro
            ? selectedLogradouro.logradouro
            : extrairLogradouro(texto);

        try {
            const params = new URLSearchParams({ logradouro });
            if (numero && numero > 0) {
                params.append('numero', numero);
            }

            const response = await fetch(`/api/enderecos/buscar?${params}`);
            const result = await response.json();

            if (result.encontrado) {
                const end = result.endereco;
                const lat = parseFloat(end.lat);
                const lng = parseFloat(end.lng);

                const numeroInformado = result.numero_informado;
                const isAproximado = !result.exato || !numeroInformado;
                const numLabel = Math.round(end.numero);
                const enderecoLabel = `${end.tipo} ${end.logradouro}, ${numLabel} - ${end.regional}`;

                // Atualiza o input com o formato completo
                searchInput.value = enderecoLabel;

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

    // Enter dispara busca
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchResults.classList.add('hidden');
            buscarEnderecoCompleto();
        }
    });

    // Navegar para o endereço no mapa
    function irParaEndereco(lat, lng, endereco, bairro, regional, isAproximado = false, mensagemAproximado = null) {
        // Remove marcador anterior se existir
        if (searchMarker) {
            map.removeLayer(searchMarker);
            searchMarker = null;
        }

        // Centraliza no ponto com zoom 18 (mesmo comportamento do clique no mapa)
        map.flyTo([lat, lng], 18);

        selectedLogradouro = null;
    }
});
</script>
@endpush
