@extends('layouts.app')

@section('title', 'Editar Morador')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('moradores.show', $morador) }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-semibold flex-1 text-center">Editar Morador</h1>
        <div class="w-10"></div>
    </div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 transition-colors duration-200">
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('moradores.update', $morador) }}" method="POST" enctype="multipart/form-data"
                  class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-6 transition-colors duration-200">
                @csrf
                @method('PUT')

                <!-- Foto -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto</label>
                    <div class="flex items-center gap-4">
                        <div id="foto-preview" class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                            @if($morador->fotografia)
                                <img src="{{ Storage::url($morador->fotografia) }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="fotografia" accept="image/*" id="foto-input"
                                   class="text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300">
                            @if($morador->fotografia)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Selecione uma nova foto para substituir a atual</p>
                            @endif
                        </div>
                    </div>
                    @error('fotografia')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nome Social -->
                <div class="mb-4">
                    <label for="nome_social" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nome Social <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nome_social" id="nome_social" value="{{ old('nome_social', $morador->nome_social) }}" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('nome_social') border-red-500 @enderror"
                           placeholder="Nome pelo qual deseja ser chamado">
                    @error('nome_social')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nome de Registro -->
                <div class="mb-4">
                    <label for="nome_registro" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nome de Registro
                    </label>
                    <input type="text" name="nome_registro" id="nome_registro" value="{{ old('nome_registro', $morador->nome_registro) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="Nome civil (se diferente)">
                </div>

                <!-- Apelido -->
                <div class="mb-4">
                    <label for="apelido" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Apelido
                    </label>
                    <input type="text" name="apelido" id="apelido" value="{{ old('apelido', $morador->apelido) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="Como e conhecido">
                </div>

                <!-- Genero -->
                <div class="mb-4">
                    <label for="genero" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Genero
                    </label>
                    <select name="genero" id="genero"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">Prefiro nao informar</option>
                        <option value="Homem cisgenero" {{ old('genero', $morador->genero) == 'Homem cisgenero' ? 'selected' : '' }}>Homem cisgenero</option>
                        <option value="Mulher cisgenero" {{ old('genero', $morador->genero) == 'Mulher cisgenero' ? 'selected' : '' }}>Mulher cisgenero</option>
                        <option value="Homem trans" {{ old('genero', $morador->genero) == 'Homem trans' ? 'selected' : '' }}>Homem trans</option>
                        <option value="Mulher trans" {{ old('genero', $morador->genero) == 'Mulher trans' ? 'selected' : '' }}>Mulher trans</option>
                        <option value="Travesti" {{ old('genero', $morador->genero) == 'Travesti' ? 'selected' : '' }}>Travesti</option>
                        <option value="Nao-binario" {{ old('genero', $morador->genero) == 'Nao-binario' ? 'selected' : '' }}>Nao-binario</option>
                        <option value="Outro" {{ old('genero', $morador->genero) == 'Outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>

                <!-- Documento -->
                <div class="mb-4">
                    <label for="documento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Documento (CPF/RG)
                    </label>
                    <input type="text" name="documento" id="documento" value="{{ old('documento', $morador->documento) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="Numero do documento">
                </div>

                <!-- Contato -->
                <div class="mb-4">
                    <label for="contato" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Contato
                    </label>
                    <input type="text" name="contato" id="contato" value="{{ old('contato', $morador->contato) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="Telefone ou outro contato">
                </div>

                <!-- Observacoes -->
                <div class="mb-6">
                    <label for="observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Observacoes
                    </label>
                    <textarea name="observacoes" id="observacoes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                              placeholder="Informacoes adicionais">{{ old('observacoes', $morador->observacoes) }}</textarea>
                </div>

                <!-- Botoes -->
                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 py-3 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 transition">
                        Salvar
                    </button>
                    <a href="{{ route('moradores.show', $morador) }}"
                       class="flex-1 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-medium text-center hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('foto-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('foto-preview');
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
