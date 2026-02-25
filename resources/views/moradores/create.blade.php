@extends('layouts.app')

@section('title', 'Novo Morador')

@section('header')
    <div class="flex items-center gap-3 flex-1">
        <a href="{{ route('moradores.index') }}" class="btn btn-ghost btn-icon" style="margin-left: -8px;">
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="mobile-header-title flex-1 text-center">Novo Morador</span>
        <div style="width: 44px;"></div>
    </div>
@endsection

@section('content')
    <div class="form-page">
        <form action="{{ route('moradores.store') }}" method="POST" enctype="multipart/form-data" class="form-container">
            @csrf

            <div class="form-content">
                {{-- Ponto vinculado --}}
                @if($ponto)
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <svg style="width: 24px; height: 24px; color: var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-muted" style="font-size: var(--text-xs);">Vinculando ao ponto</p>
                                    <p style="font-weight: var(--font-medium);">
                                        {{ $ponto->enderecoAtualizado->SIGLA_TIPO_LOGRADOURO ?? '' }}
                                        {{ $ponto->enderecoAtualizado->NOME_LOGRADOURO ?? '' }},
                                        {{ $ponto->enderecoAtualizado->NUMERO_IMOVEL ?? $ponto->numero }}
                                    </p>
                                    <p class="text-muted" style="font-size: var(--text-xs);">
                                        {{ $ponto->enderecoAtualizado->NOME_BAIRRO_OFICIAL ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="ponto_id" value="{{ $ponto->id }}">
                @endif

                <!-- Foto do Morador -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="form-section-title">Foto</h3>
                        <div class="flex items-center gap-4">
                            <div id="foto-preview" class="avatar avatar-xl">
                                <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-2">
                                <input type="file" id="camera-input" accept="image/*" capture="user" class="hidden">
                                <input type="file" id="gallery-input" name="fotografia" accept="image/*" class="hidden">

                                <button type="button" onclick="document.getElementById('camera-input').click()" class="btn btn-primary btn-sm">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Tirar Foto
                                </button>
                                <button type="button" onclick="document.getElementById('gallery-input').click()" class="btn btn-secondary btn-sm">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Galeria
                                </button>
                            </div>
                        </div>
                        @error('fotografia')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Identificacao -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="form-section-title">Identificacao</h3>

                        <div class="form-group">
                            <label for="nome_social" class="form-label required">Nome Social</label>
                            <div class="input-with-voice">
                                <input type="text" name="nome_social" id="nome_social" value="{{ old('nome_social') }}" required
                                       class="form-input @error('nome_social') is-invalid @enderror"
                                       placeholder="Nome pelo qual deseja ser chamado">
                                <button type="button" onclick="startVoiceInput('nome_social')" class="voice-btn">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('nome_social')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nome_registro" class="form-label">Nome de Registro</label>
                            <div class="input-with-voice">
                                <input type="text" name="nome_registro" id="nome_registro" value="{{ old('nome_registro') }}"
                                       class="form-input"
                                       placeholder="Nome civil (se diferente)">
                                <button type="button" onclick="startVoiceInput('nome_registro')" class="voice-btn">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="apelido" class="form-label">Apelido</label>
                            <div class="input-with-voice">
                                <input type="text" name="apelido" id="apelido" value="{{ old('apelido') }}"
                                       class="form-input"
                                       placeholder="Como e conhecido">
                                <button type="button" onclick="startVoiceInput('apelido')" class="voice-btn">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="genero" class="form-label">Genero</label>
                            <select name="genero" id="genero" class="form-input form-select">
                                <option value="">Prefiro nao informar</option>
                                <option value="Homem cisgenero" {{ old('genero') == 'Homem cisgenero' ? 'selected' : '' }}>Homem cisgenero</option>
                                <option value="Mulher cisgenero" {{ old('genero') == 'Mulher cisgenero' ? 'selected' : '' }}>Mulher cisgenero</option>
                                <option value="Homem trans" {{ old('genero') == 'Homem trans' ? 'selected' : '' }}>Homem trans</option>
                                <option value="Mulher trans" {{ old('genero') == 'Mulher trans' ? 'selected' : '' }}>Mulher trans</option>
                                <option value="Travesti" {{ old('genero') == 'Travesti' ? 'selected' : '' }}>Travesti</option>
                                <option value="Nao-binario" {{ old('genero') == 'Nao-binario' ? 'selected' : '' }}>Nao-binario</option>
                                <option value="Outro" {{ old('genero') == 'Outro' ? 'selected' : '' }}>Outro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contato e Documentos -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="form-section-title">Contato e Documentos</h3>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="form-group">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="text" name="documento" id="documento" value="{{ old('documento') }}"
                                       class="form-input"
                                       placeholder="CPF ou RG">
                            </div>

                            <div class="form-group">
                                <label for="contato" class="form-label">Contato</label>
                                <input type="text" name="contato" id="contato" value="{{ old('contato') }}"
                                       class="form-input"
                                       placeholder="Telefone">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observacoes -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="form-section-title">Observacoes</h3>

                        <div class="form-group">
                            <div class="input-with-voice">
                                <textarea name="observacoes" id="observacoes" rows="3"
                                          class="form-input form-textarea"
                                          placeholder="Informacoes adicionais relevantes...">{{ old('observacoes') }}</textarea>
                                <button type="button" onclick="startVoiceInput('observacoes')" class="voice-btn">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botoes fixos --}}
            <div class="form-actions">
                <a href="{{ route('moradores.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Cadastrar
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
// Preview de foto da camera ou galeria
['camera-input', 'gallery-input'].forEach(function(inputId) {
    document.getElementById(inputId).addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Copiar arquivo para o input hidden de envio
            if (inputId === 'camera-input') {
                const galleryInput = document.getElementById('gallery-input');
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                galleryInput.files = dataTransfer.files;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('foto-preview');
                preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">`;
            };
            reader.readAsDataURL(file);
        }
    });
});

// Voice input
let recognition = null;
let currentField = null;

function startVoiceInput(fieldId) {
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        alert('Reconhecimento de voz nao suportado neste navegador.');
        return;
    }

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.lang = 'pt-BR';
    recognition.continuous = false;
    recognition.interimResults = false;

    currentField = document.getElementById(fieldId);
    const voiceBtn = currentField.parentElement.querySelector('.voice-btn');

    recognition.onstart = function() {
        voiceBtn.classList.add('recording');
    };

    recognition.onend = function() {
        voiceBtn.classList.remove('recording');
    };

    recognition.onresult = function(event) {
        const transcript = event.results[0][0].transcript;
        if (currentField.tagName === 'TEXTAREA') {
            currentField.value += (currentField.value ? '\n' : '') + transcript;
        } else {
            currentField.value = transcript;
        }
    };

    recognition.onerror = function(event) {
        voiceBtn.classList.remove('recording');
        console.error('Erro no reconhecimento de voz:', event.error);
    };

    recognition.start();
}
</script>
@endpush
