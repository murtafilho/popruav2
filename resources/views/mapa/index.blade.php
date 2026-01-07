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
</style>
@endpush

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-80 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <h1 class="text-lg font-semibold">POPRUA</h1>
        </a>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="toggleDarkMode()" class="p-2 rounded-lg hover:bg-white/10 transition" title="Alternar tema">
            <svg data-light-icon class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg data-dark-icon class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>
        <button id="btn-menu" class="p-2 rounded-lg hover:bg-white/10 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
@endsection

@section('content')
    <!-- Map Container -->
    <div id="map"></div>

    <!-- Indicador de Zoom -->
    <div id="zoom-indicator" class="absolute bottom-20 left-4 z-[1000] bg-white/90 dark:bg-gray-800/90 text-gray-200 dark:text-gray-200 px-2 py-1 rounded shadow text-xs font-mono transition-colors duration-200">
        Zoom: <span id="zoom-level">12</span>
    </div>

    <!-- FAB - Minha Localização -->
    <button
        id="btn-my-location"
        class="absolute bottom-20 right-4 z-[1000] w-12 h-12 bg-white dark:bg-gray-800 text-gray-200 dark:text-gray-200 rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition active:scale-95"
        title="Minha localização"
    >
        <svg id="location-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <svg id="location-loader" class="w-6 h-6 hidden animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </button>

    <!-- Layers Panel (abre pelo menu) -->
    <div id="layers-panel" class="absolute top-14 right-2 z-[1000] bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900/50 p-3 hidden max-h-[80vh] overflow-y-auto transition-colors duration-200">
        <h4 class="text-sm font-semibold mb-2 text-gray-900 dark:text-white">Mapa Base</h4>
        <div class="space-y-1 mb-3">
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="radio" name="base-layer" id="base-street" class="text-blue-500">
                <span class="text-gray-700 dark:text-gray-200">Ruas</span>
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="radio" name="base-layer" id="base-satellite" class="text-blue-500" checked>
                <span class="text-gray-700 dark:text-gray-200">Satélite</span>
            </label>
        </div>

        <h4 class="text-sm font-semibold mb-2 text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-700 pt-2">Camadas</h4>
        <div class="space-y-2 mb-3">
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" id="layer-regionais" class="rounded text-blue-500">
                <span class="text-gray-700 dark:text-gray-200">Regionais</span>
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" id="layer-bairros" class="rounded text-blue-500">
                <span class="text-gray-700 dark:text-gray-200">Bairros</span>
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" id="layer-limite" class="rounded text-blue-500" checked>
                <span class="text-gray-700 dark:text-gray-200">Limite Municipal</span>
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" id="layer-pontos" class="rounded text-blue-500" checked>
                <span class="text-gray-700 dark:text-gray-200">Pontos</span>
            </label>
        </div>

        <h4 class="text-sm font-semibold mb-2 text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-700 pt-2">Filtrar por Resultado</h4>
        <div class="space-y-1 text-xs">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" data-resultado="1" class="filter-resultado rounded" checked>
                <span class="w-3 h-3 rounded-full bg-[#dc2626]"></span>
                <span class="text-gray-700 dark:text-gray-200">Fenômeno persiste</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" data-resultado="2" class="filter-resultado rounded" checked>
                <span class="w-3 h-3 rounded-full bg-[#f97316]"></span>
                <span class="text-gray-700 dark:text-gray-200">Impactado parcialmente</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" data-resultado="3" class="filter-resultado rounded">
                <span class="w-3 h-3 rounded-full bg-[#1f2937]"></span>
                <span class="text-gray-700 dark:text-gray-200">Deixou de Ocorrer</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" data-resultado="4" class="filter-resultado rounded" checked>
                <span class="w-3 h-3 rounded-full bg-[#6b7280]"></span>
                <span class="text-gray-700 dark:text-gray-200">PSR ausente</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" data-resultado="5" class="filter-resultado rounded">
                <span class="w-3 h-3 rounded-full bg-[#3b82f6]"></span>
                <span class="text-gray-700 dark:text-gray-200">Não constatado</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" data-resultado="6" class="filter-resultado rounded" checked>
                <span class="w-3 h-3 rounded-full bg-[#10b981]"></span>
                <span class="text-gray-700 dark:text-gray-200">Em Conformidade</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" data-resultado="null" class="filter-resultado rounded" checked>
                <span class="w-3 h-3 rounded-full bg-[#a855f7]"></span>
                <span class="text-gray-700 dark:text-gray-200">Sem vistoria</span>
            </label>
        </div>
    </div>

    <!-- Bottom Sheet - Point Info -->
    <div id="bottom-sheet" class="absolute bottom-0 left-0 right-0 z-[1000] bg-white dark:bg-gray-800 rounded-t-2xl shadow-2xl transform translate-y-full transition-all duration-300">
        <div class="p-4">
            <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-4"></div>
            <div id="sheet-content">
                <!-- Content will be dynamically inserted -->
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <div class="flex justify-around">
        <button class="flex flex-col items-center text-blue-500 text-xs py-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span>Mapa</span>
        </button>
        <button id="btn-vistoria" class="flex flex-col items-center text-gray-300 text-xs py-1 cursor-not-allowed" disabled>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span>Vistoria</span>
        </button>
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center text-gray-500 text-xs py-1 hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Perfil</span>
        </a>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Belo Horizonte center coordinates (média dos pontos no banco)
    const BH_CENTER = [-19.9135, -43.9514];
    const DEFAULT_ZOOM = 12;

    // Verificar se há parâmetros de localização na URL
    const urlParams = new URLSearchParams(window.location.search);
    const lat = urlParams.get('lat');
    const lng = urlParams.get('lng');
    const zoom = urlParams.get('zoom') ? parseInt(urlParams.get('zoom')) : DEFAULT_ZOOM;
    const pontoId = urlParams.get('ponto_id');
    const geocoded = urlParams.get('geocoded') === '1';

    // Initialize map
    const map = L.map('map', {
        zoomControl: false
    });

    // Add zoom control to bottom-left (better for mobile)
    L.control.zoom({ position: 'bottomleft' }).addTo(map);

    // Atualizar indicador de zoom
    const zoomLevel = document.getElementById('zoom-level');
    zoomLevel.textContent = map.getZoom();
    map.on('zoomend', () => {
        zoomLevel.textContent = map.getZoom();
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

                selectedPointMarker.bindPopup('Ponto selecionado').openPopup();
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
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';

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
                    // Sucesso - redireciona para lista com mensagem
                    window.location.href = '/pontos/nao-georreferenciados?success=1';
                } else {
                    alert('Erro ao salvar coordenadas: ' + (data.message || 'Erro desconhecido'));
                    btn.disabled = false;
                    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Confirmar';
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao salvar coordenadas. Tente novamente.');
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Confirmar';
            }
        });
    }

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
                    className: 'bg-white px-2 py-1 rounded shadow text-sm'
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
                        html: `<span class="px-2 py-1 bg-blue-500/90 text-white text-xs font-bold rounded shadow-sm whitespace-nowrap">${feature.properties.nome}</span>`,
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
                    className: 'bg-white px-2 py-1 rounded shadow text-xs'
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
                        html: `<span class="px-2 py-1 bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-gray-200 text-xs font-medium rounded shadow-sm whitespace-nowrap">${feature.properties.nome}</span>`,
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

                        const marker = L.circleMarker([lat, lng], {
                            radius: 8,
                            fillColor: cor,
                            color: '#fff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.9
                        }).bindPopup(`
                                <strong>${ponto.logradouro}, ${ponto.numero}</strong><br>
                                <small>${ponto.bairro} - ${ponto.regional}</small><br>
                                <span style="color:${cor}; font-weight:bold;">● ${status}</span>
                            `);
                        marker.pontoData = ponto;
                        marker.resultadoId = resultadoId; // Armazena para filtro
                        marker.on('click', () => showPointDetails(ponto));
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
        // Mostra loading
        showBottomSheet(`
            <div class="flex items-center justify-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
        `);

        // Busca detalhes completos do ponto
        try {
            const response = await fetch(`/api/pontos/${ponto.id}`);
            const details = await response.json();

            showBottomSheet(`
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">${details.logradouro}, ${details.numero}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">${details.bairro} - ${details.regional}</p>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-2 text-center">
                        <div class="text-lg font-bold text-blue-500">${details.contador || 0}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Vistorias</div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-2 text-center">
                        <div class="text-lg font-bold text-blue-500">${details.soma_kg || 0}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Kg</div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-2 text-center">
                        <div class="text-lg font-bold text-blue-500">${details.complexidade || 0}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Complex.</div>
                    </div>
                </div>
                <button
                    onclick="window.location.href='/pontos/${details.id}/vistorias/create'"
                    class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition"
                >
                    Nova Vistoria
                </button>
            `);
        } catch (err) {
            showBottomSheet(`
                <p class="text-red-500 text-center">Erro ao carregar detalhes</p>
            `);
        }
    }

    // Geolocation - botão FAB
    let userLocationMarker = null;
    let accuracyCircle = null;
    let locationMode = false; // Modo de seleção de localização ativo
    let currentLocation = null; // Armazena lat/lng atual

    const btnVistoria = document.getElementById('btn-vistoria');

    function enableVistoriaButton() {
        btnVistoria.disabled = false;
        btnVistoria.classList.remove('text-gray-300', 'cursor-not-allowed');
        btnVistoria.classList.add('text-blue-500');
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
            btn.classList.remove('text-blue-500');
            btn.style.pointerEvents = 'auto';
        };
        
        // Mostra loader e esconde ícone
        if (icon) icon.classList.add('hidden');
        if (loader) loader.classList.remove('hidden');
        btn.classList.add('text-blue-500');
        btn.style.pointerEvents = 'none'; // Desabilita cliques temporariamente

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    setUserLocation(lat, lng, position.coords.accuracy);
                    map.setView([lat, lng], 17);

                    locationMode = true; // Ativa modo de seleção
                    
                    // Restaura botão com cor primária
                    restoreButton();
                    btn.classList.add('text-blue-500');
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

    // Clique no mapa cria ponto provisório, ajusta zoom e habilita vistoria
    map.on('click', function(e) {
        // Cria/move o marcador provisório
        setUserLocation(e.latlng.lat, e.latlng.lng);

        // Ajusta zoom para 17 se estiver menor
        if (map.getZoom() < 17) {
            map.setView(e.latlng, 17);
        }

        // Ativa o modo de seleção
        locationMode = true;

        hideBottomSheet();
    });

    // Botão de vistoria
    btnVistoria.addEventListener('click', function() {
        if (!currentLocation) return;

        showBottomSheet(`
            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Nova Vistoria</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                <strong>Localização:</strong><br>
                Lat: ${currentLocation.lat.toFixed(6)}<br>
                Lng: ${currentLocation.lng.toFixed(6)}
            </p>
            <div class="space-y-3">
                <a href="/vistorias/create?lat=${currentLocation.lat}&lng=${currentLocation.lng}"
                   class="block w-full bg-blue-500 text-white py-3 rounded-lg font-medium text-center hover:bg-blue-600 transition">
                    Registrar Vistoria
                </a>
                <button onclick="hideBottomSheet()" class="w-full bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-3 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 transition border border-gray-300 dark:border-gray-600">
                    Cancelar
                </button>
            </div>
        `);
    });

    // Expor hideBottomSheet globalmente para o onclick inline
    window.hideBottomSheet = hideBottomSheet;

    // Solicitar localização automaticamente se vier de "Nova Vistoria"
    if (urlParams.get('nova_vistoria') === '1') {
        // Aguarda o mapa estar totalmente carregado
        map.whenReady(() => {
            // Pequeno delay para garantir que tudo está pronto
            setTimeout(() => {
                // Simula o clique no botão de localização
                const btn = document.getElementById('btn-my-location');
                if (btn) {
                    btn.click();
                }
            }, 500);
        });
    }
});
</script>
@endpush
