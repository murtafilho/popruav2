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
    <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <form action="{{ route('vistorias.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-4">
            @csrf
            <input type="hidden" name="lat" value="{{ $lat }}">
            <input type="hidden" name="lng" value="{{ $lng }}">
            @if($pontoProximo)
                <input type="hidden" name="ponto_id" value="{{ $pontoProximo->id }}">
            @endif

            <!-- Localização -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm transition-colors duration-200">
                <h3 class="font-semibold text-gray-200 dark:text-gray-300 mb-2">Localização</h3>
                @if($pontoProximo)
                    <p class="text-sm text-green-600">
                        <span class="font-medium">Ponto existente:</span>
                        {{ $pontoProximo->logradouro }}, {{ $pontoProximo->numero }} - {{ $pontoProximo->bairro }}
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

            <!-- Dados Básicos -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm space-y-3 transition-colors duration-200">
                <h3 class="font-semibold text-gray-200 dark:text-gray-300">Dados da Vistoria</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-1">Data/Hora da Abordagem *</label>
                    <input type="datetime-local" name="data_abordagem" value="{{ date('Y-m-d\TH:i') }}" required
                           class="w-full px-4 py-3 bg-[#1e2939] text-white text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#1e2939] transition-all duration-200">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-1">Tipo de Abordagem *</label>
                    <select name="tipo_abordagem_id" required
                            class="w-full px-4 py-3 bg-[#1e2939] text-white text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#1e2939] transition-all duration-200">
                        <option value="">Selecione...</option>
                        @foreach($tiposAbordagem as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-1">Resultado da Ação *</label>
                    <select name="resultado_acao_id" required
                            class="w-full px-4 py-3 bg-[#1e2939] text-white text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#1e2939] transition-all duration-200">
                        <option value="">Selecione...</option>
                        @foreach($resultadosAcao as $resultado)
                            <option value="{{ $resultado->id }}">{{ $resultado->resultado }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Qtd. Pessoas</label>
                        <input type="number" name="quantidade_pessoas" min="0" value="0"
                               class="w-full px-4 py-3 bg-[#1e2939] text-white text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#1e2939] transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Qtd. Kg</label>
                        <input type="number" name="qtd_kg" min="0" value="0"
                               class="w-full px-4 py-3 bg-[#1e2939] text-white text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#1e2939] transition-all duration-200">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-1">Nomes das Pessoas</label>
                    <div class="relative">
                        <input type="text" name="nomes_pessoas" id="nomes_pessoas" placeholder="Separe por vírgula"
                               class="w-full px-4 py-3 pr-12 bg-[#1e2939] text-white text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#1e2939] placeholder:text-gray-400 placeholder:opacity-70 transition-all duration-200">
                        <button type="button" onclick="startVoiceInput('nomes_pessoas')"
                                class="voice-btn absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-500 hover:text-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-1">Tipo de Abrigo Desmontado</label>
                    <select name="tipo_abrigo_desmontado_id"
                            class="w-full px-4 py-3 bg-[#1e2939] text-white text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#1e2939] transition-all duration-200">
                        <option value="">Nenhum</option>
                        @foreach($tiposAbrigo as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->tipo_abrigo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Complexidade -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm transition-colors duration-200">
                <h3 class="font-semibold text-gray-200 dark:text-gray-300 mb-3">Fatores de Complexidade</h3>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="resistencia" value="1" class="rounded text-primary w-5 h-5">
                        <span>Resistência</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="num_reduzido" value="1" class="rounded text-primary w-5 h-5">
                        <span>Núm. Reduzido</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="casal" value="1" class="rounded text-primary w-5 h-5">
                        <span>Casal</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="catador_reciclados" value="1" class="rounded text-primary w-5 h-5">
                        <span>Catador</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="fixacao_antiga" value="1" class="rounded text-primary w-5 h-5">
                        <span>Fixação Antiga</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="estrutura_abrigo_provisorio" value="1" class="rounded text-primary w-5 h-5">
                        <span>Abrigo Provisório</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="excesso_objetos" value="1" class="rounded text-primary w-5 h-5">
                        <span>Excesso Objetos</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="trafico_ilicitos" value="1" class="rounded text-primary w-5 h-5">
                        <span>Tráfico/Ilícitos</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="menores_idosos" value="1" class="rounded text-primary w-5 h-5">
                        <span>Menores/Idosos</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="deficiente" value="1" class="rounded text-primary w-5 h-5">
                        <span>Deficiente</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="agrupamento_quimico" value="1" class="rounded text-primary w-5 h-5">
                        <span>Agrup. Químico</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors">
                        <input type="checkbox" name="saude_mental" value="1" class="rounded text-primary w-5 h-5">
                        <span>Saúde Mental</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm p-2 bg-gray-100 rounded-lg border border-gray-300 cursor-pointer hover:bg-gray-200 transition-colors col-span-2">
                        <input type="checkbox" name="animais" value="1" class="rounded text-primary w-5 h-5">
                        <span>Animais</span>
                    </label>
                </div>
            </div>

            <!-- Observação -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm transition-colors duration-200">
                <label class="block text-sm font-medium text-gray-200 dark:text-gray-300 mb-1">Observações</label>
                <div class="relative">
                    <textarea name="observacao" id="observacao" rows="3" placeholder="Observações adicionais..."
                              class="w-full px-4 py-3 pr-12 bg-[#1e2939] text-white text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#1e2939] placeholder:text-gray-400 placeholder:opacity-70 transition-all duration-200 resize-none"></textarea>
                    <button type="button" onclick="startVoiceInput('observacao')"
                            class="voice-btn absolute right-2 top-3 p-2 text-gray-500 dark:text-gray-400 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Fotos -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm transition-colors duration-200">
                <label class="block text-sm font-medium text-gray-200 dark:text-gray-300 mb-3">Fotos da Vistoria</label>
                
                <!-- Input de câmera (oculto) - Fallback para navegadores que não suportam getUserMedia -->
                <input type="file" id="camera-input-back" accept="image/*" capture="environment" class="hidden">
                <input type="file" id="camera-input-front" accept="image/*" capture="user" class="hidden">
                
                <!-- Input de galeria (oculto) -->
                <input type="file" id="gallery-input" accept="image/*" multiple class="hidden">
                
                <!-- Botões para acionar câmera -->
                <div class="space-y-2">
                    <button type="button" onclick="openCamera('back')" 
                            class="w-full py-3 px-4 bg-blue-500 dark:bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-600 dark:hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Tirar Foto (Câmera Traseira)</span>
                    </button>
                    
                    <button type="button" onclick="openCamera('front')" 
                            class="w-full py-3 px-4 bg-gray-500 dark:bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-600 dark:hover:bg-gray-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Tirar Foto (Câmera Frontal)</span>
                    </button>
                    
                    <button type="button" onclick="openGallery()" 
                            class="w-full py-3 px-4 bg-green-500 dark:bg-green-600 text-white rounded-lg font-medium hover:bg-green-600 dark:hover:bg-green-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Escolher da Galeria</span>
                    </button>
                </div>

                <!-- Preview das fotos -->
                <div id="fotos-preview" class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <!-- Fotos serão inseridas aqui via JavaScript -->
                </div>
                
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    Você pode adicionar múltiplas fotos. Use a câmera ou escolha da galeria.
                </p>
            </div>

            <!-- Botões -->
            <div class="space-y-2 pb-4">
                <button type="submit"
                        class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary-dark transition active:scale-98">
                    Registrar Vistoria
                </button>
                <a href="{{ route('mapa.index') }}"
                   class="block w-full bg-gray-200 text-gray-200 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        let recognition = null;
        let activeInput = null;
        let fotosSelecionadas = []; // Array para armazenar as fotos

        // Verificar se é dispositivo móvel
        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
                   (navigator.maxTouchPoints && navigator.maxTouchPoints > 2 && /MacIntel/.test(navigator.platform));
        }

        // Função para abrir a câmera usando MediaDevices API (mais confiável)
        function openCameraWithAPI(type = 'back') {
            const facingMode = type === 'back' ? 'environment' : 'user';
            
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                // Fallback para input file
                openCameraInput(type);
                return;
            }

            const constraints = {
                video: {
                    facingMode: facingMode,
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                }
            };

            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(stream) {
                    // Criar elemento de vídeo temporário
                    const video = document.createElement('video');
                    video.srcObject = stream;
                    video.autoplay = true;
                    video.playsInline = true;
                    video.style.width = '100%';
                    video.style.maxHeight = '400px';
                    video.style.objectFit = 'contain';

                    // Criar modal para preview da câmera
                    const modal = document.createElement('div');
                    modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex flex-col items-center justify-center p-4';
                    modal.innerHTML = `
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 w-full max-w-md">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Tire uma foto</h3>
                            <div id="camera-preview" class="mb-4 bg-black rounded-lg overflow-hidden"></div>
                            <div class="flex gap-2">
                                <button id="capture-btn" class="flex-1 bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600 transition">
                                    Capturar
                                </button>
                                <button id="cancel-camera-btn" class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-200 dark:text-gray-200 py-3 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    const preview = modal.querySelector('#camera-preview');
                    preview.appendChild(video);

                    // Canvas para capturar a foto
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Botão de capturar
                    modal.querySelector('#capture-btn').addEventListener('click', function() {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        ctx.drawImage(video, 0, 0);
                        
                        // Converter para blob
                        canvas.toBlob(function(blob) {
                            const file = new File([blob], 'foto-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                            processPhotoFile(file);
                            
                            // Parar a stream e remover modal
                            stream.getTracks().forEach(track => track.stop());
                            document.body.removeChild(modal);
                        }, 'image/jpeg', 0.9);
                    });

                    // Botão de cancelar
                    modal.querySelector('#cancel-camera-btn').addEventListener('click', function() {
                        stream.getTracks().forEach(track => track.stop());
                        document.body.removeChild(modal);
                    });
                })
                .catch(function(error) {
                    console.error('Erro ao acessar câmera:', error);
                    // Fallback para input file
                    openCameraInput(type);
                });
        }

        // Função fallback para abrir input file
        function openCameraInput(type = 'back') {
            const inputId = type === 'back' ? 'camera-input-back' : 'camera-input-front';
            const input = document.getElementById(inputId);
            
            if (!input) {
                alert('Câmera não disponível neste dispositivo.');
                return;
            }
            
            // Limpa o valor para permitir selecionar a mesma foto novamente
            input.value = '';
            input.click();
        }

        // Função para abrir a câmera (traseira ou frontal)
        function openCamera(type = 'back') {
            // Se for dispositivo móvel, tenta usar a API primeiro
            if (isMobileDevice()) {
                // Em mobile, tenta API primeiro, depois fallback para input
                openCameraWithAPI(type);
            } else {
                // Em desktop, usa input file (que pode abrir webcam em alguns navegadores)
                openCameraInput(type);
            }
        }

        // Função para abrir a galeria
        function openGallery() {
            const input = document.getElementById('gallery-input');
            if (!input) {
                alert('Galeria não disponível.');
                return;
            }
            input.value = '';
            input.click();
        }

        // Função para processar arquivo de foto
        function processPhotoFile(file) {
            if (!file.type.startsWith('image/')) {
                alert('Por favor, selecione apenas arquivos de imagem.');
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const fotoData = {
                    file: file,
                    preview: e.target.result,
                    id: Date.now() + Math.random()
                };
                
                fotosSelecionadas.push(fotoData);
                renderFotosPreview();
            };
            
            reader.onerror = function() {
                alert('Erro ao ler o arquivo. Tente novamente.');
            };
            
            reader.readAsDataURL(file);
        }

        // Quando uma foto é capturada pela câmera traseira
        document.getElementById('camera-input-back').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                processPhotoFile(file);
            }
            // Limpa o input para permitir capturar novamente
            e.target.value = '';
        });

        // Quando uma foto é capturada pela câmera frontal
        document.getElementById('camera-input-front').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                processPhotoFile(file);
            }
            // Limpa o input para permitir capturar novamente
            e.target.value = '';
        });

        // Quando fotos são selecionadas da galeria
        document.getElementById('gallery-input').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            files.forEach(file => {
                processPhotoFile(file);
            });
            
            // Limpa o input para permitir selecionar novamente
            e.target.value = '';
        });

        // Renderiza o preview das fotos
        function renderFotosPreview() {
            const container = document.getElementById('fotos-preview');
            container.innerHTML = '';
            
            fotosSelecionadas.forEach((foto, index) => {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <img src="${foto.preview}" alt="Foto ${index + 1}" 
                         class="w-full h-32 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="removerFoto(${index})" 
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <input type="file" name="fotos[]" class="hidden" data-foto-index="${index}">
                `;
                container.appendChild(div);
            });
            
            // Atualiza os inputs de arquivo
            updateFileInputs();
        }

        // Remove uma foto
        function removerFoto(index) {
            fotosSelecionadas.splice(index, 1);
            renderFotosPreview();
        }

        // Atualiza os inputs de arquivo do formulário
        function updateFileInputs() {
            // Cria um FormData para enviar as fotos
            const formData = new FormData();
            
            fotosSelecionadas.forEach((foto, index) => {
                formData.append(`fotos[${index}]`, foto.file);
            });
            
            // Armazena o FormData para ser enviado junto com o formulário
            window.fotosFormData = formData;
        }

        // Intercepta o submit do formulário para incluir as fotos
        document.querySelector('form').addEventListener('submit', function(e) {
            // Adiciona as fotos ao FormData do formulário
            fotosSelecionadas.forEach((foto, index) => {
                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'fotos[]';
                input.style.display = 'none';
                
                // Cria um DataTransfer para adicionar o arquivo
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(foto.file);
                input.files = dataTransfer.files;
                
                this.appendChild(input);
            });
        });

        function startVoiceInput(inputId) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (!SpeechRecognition) {
                alert('Seu navegador não suporta reconhecimento de voz. Use Chrome, Safari ou Edge.');
                return;
            }

            // Se já está gravando, para
            if (recognition && activeInput === inputId) {
                recognition.stop();
                return;
            }

            // Para qualquer gravação anterior
            if (recognition) {
                recognition.stop();
            }

            recognition = new SpeechRecognition();
            recognition.lang = 'pt-BR';
            recognition.continuous = false;
            recognition.interimResults = true;

            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('.voice-btn');
            activeInput = inputId;

            // Visual feedback - gravando
            button.classList.add('text-red-500', 'animate-pulse');
            button.classList.remove('text-gray-500');

            recognition.onresult = (event) => {
                let transcript = '';
                for (let i = 0; i < event.results.length; i++) {
                    transcript += event.results[i][0].transcript;
                }

                // Adiciona ao conteúdo existente
                if (input.value && !input.value.endsWith(' ')) {
                    input.value += ' ';
                }
                input.value = input.value.trimEnd() + (input.value ? ' ' : '') + transcript;
            };

            recognition.onend = () => {
                button.classList.remove('text-red-500', 'animate-pulse');
                button.classList.add('text-gray-500');
                activeInput = null;
            };

            recognition.onerror = (event) => {
                console.error('Erro no reconhecimento:', event.error);
                button.classList.remove('text-red-500', 'animate-pulse');
                button.classList.add('text-gray-500');
                activeInput = null;

                if (event.error === 'not-allowed') {
                    alert('Permissão de microfone negada. Habilite nas configurações do navegador.');
                }
            };

            recognition.start();
        }
    </script>

    <style>
        .animate-pulse {
            animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
    </div>
@endsection
