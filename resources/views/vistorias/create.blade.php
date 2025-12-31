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
    <div class="h-full overflow-y-auto bg-gray-50">
        <form action="{{ route('vistorias.store') }}" method="POST" class="p-4 space-y-4">
            @csrf
            <input type="hidden" name="lat" value="{{ $lat }}">
            <input type="hidden" name="lng" value="{{ $lng }}">
            @if($pontoProximo)
                <input type="hidden" name="ponto_id" value="{{ $pontoProximo->id }}">
            @endif

            <!-- Localização -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h3 class="font-semibold text-gray-700 mb-2">Localização</h3>
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
            <div class="bg-white rounded-lg p-4 shadow-sm space-y-3">
                <h3 class="font-semibold text-gray-700">Dados da Vistoria</h3>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data/Hora da Abordagem *</label>
                    <input type="datetime-local" name="data_abordagem" value="{{ date('Y-m-d\TH:i') }}" required
                           class="w-full px-3 py-2.5 bg-gray-100 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Abordagem *</label>
                    <select name="tipo_abordagem_id" required
                            class="w-full px-3 py-2.5 bg-gray-100 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white transition-colors">
                        <option value="">Selecione...</option>
                        @foreach($tiposAbordagem as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resultado da Ação *</label>
                    <select name="resultado_acao_id" required
                            class="w-full px-3 py-2.5 bg-gray-100 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white transition-colors">
                        <option value="">Selecione...</option>
                        @foreach($resultadosAcao as $resultado)
                            <option value="{{ $resultado->id }}">{{ $resultado->resultado }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qtd. Pessoas</label>
                        <input type="number" name="quantidade_pessoas" min="0" value="0"
                               class="w-full px-3 py-2.5 bg-gray-100 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qtd. Kg</label>
                        <input type="number" name="qtd_kg" min="0" value="0"
                               class="w-full px-3 py-2.5 bg-gray-100 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomes das Pessoas</label>
                    <div class="relative">
                        <input type="text" name="nomes_pessoas" id="nomes_pessoas" placeholder="Separe por vírgula"
                               class="w-full px-3 py-2.5 pr-12 bg-gray-100 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white transition-colors placeholder:text-gray-400">
                        <button type="button" onclick="startVoiceInput('nomes_pessoas')"
                                class="voice-btn absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-500 hover:text-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Abrigo Desmontado</label>
                    <select name="tipo_abrigo_desmontado_id"
                            class="w-full px-3 py-2.5 bg-gray-100 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white transition-colors">
                        <option value="">Nenhum</option>
                        @foreach($tiposAbrigo as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->tipo_abrigo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Complexidade -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h3 class="font-semibold text-gray-700 mb-3">Fatores de Complexidade</h3>
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
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <div class="relative">
                    <textarea name="observacao" id="observacao" rows="3" placeholder="Observações adicionais..."
                              class="w-full px-3 py-2.5 pr-12 bg-gray-100 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary focus:bg-white transition-colors placeholder:text-gray-400"></textarea>
                    <button type="button" onclick="startVoiceInput('observacao')"
                            class="voice-btn absolute right-2 top-3 p-2 text-gray-500 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Botões -->
            <div class="space-y-2 pb-4">
                <button type="submit"
                        class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary-dark transition active:scale-98">
                    Registrar Vistoria
                </button>
                <a href="{{ route('mapa.index') }}"
                   class="block w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        let recognition = null;
        let activeInput = null;

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
