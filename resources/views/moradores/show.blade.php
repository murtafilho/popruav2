@extends('layouts.app')

@section('title', $morador->nome_social)

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('moradores.index') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-semibold flex-1 text-center truncate">{{ $morador->nome_social }}</h1>
        <a href="{{ route('moradores.edit', $morador) }}" class="p-2 rounded-lg hover:bg-white/10 transition" title="Editar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
    </div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 transition-colors duration-200">
        <!-- Mensagens -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Card Principal -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-6 mb-4 transition-colors duration-200">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- Foto -->
                <div class="flex-shrink-0">
                    @if($morador->fotografia)
                        <img src="{{ Storage::url($morador->fotografia) }}"
                             alt="{{ $morador->nome_social }}"
                             class="w-32 h-32 rounded-full object-cover shadow-lg">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center shadow-lg">
                            <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Dados -->
                <div class="flex-1 text-center sm:text-left">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $morador->nome_social }}
                    </h2>

                    @if($morador->apelido)
                        <p class="text-lg text-gray-600 dark:text-gray-400">"{{ $morador->apelido }}"</p>
                    @endif

                    @if($morador->nome_registro && $morador->nome_registro !== $morador->nome_social)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Nome de registro: {{ $morador->nome_registro }}
                        </p>
                    @endif

                    <div class="mt-4 space-y-2">
                        @if($morador->genero)
                            <div class="flex items-center justify-center sm:justify-start gap-2 text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Genero:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $morador->genero }}</span>
                            </div>
                        @endif

                        @if($morador->documento)
                            <div class="flex items-center justify-center sm:justify-start gap-2 text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Documento:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $morador->documento }}</span>
                            </div>
                        @endif

                        @if($morador->contato)
                            <div class="flex items-center justify-center sm:justify-start gap-2 text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Contato:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $morador->contato }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($morador->observacoes)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observacoes</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm whitespace-pre-wrap">{{ $morador->observacoes }}</p>
                </div>
            @endif
        </div>

        <!-- Ponto Atual -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-4 mb-4 transition-colors duration-200">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
                Ponto Atual
            </h3>
            @if($morador->pontoAtual)
                <a href="{{ route('pontos.show', $morador->pontoAtual->id) }}"
                   class="block p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                    <p class="font-medium text-blue-900 dark:text-blue-100">
                        {{ $morador->pontoAtual->endereco->logradouro ?? 'Endereco' }}, {{ $morador->pontoAtual->numero }}
                    </p>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        {{ $morador->pontoAtual->endereco->bairro ?? '' }} - {{ $morador->pontoAtual->endereco->regional ?? '' }}
                    </p>
                </a>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Nenhum ponto vinculado atualmente.</p>
            @endif
        </div>

        <!-- Historico de Movimentacao -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-4 mb-4 transition-colors duration-200">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Historico de Movimentacao
            </h3>

            @if($historico->count() > 0)
                <div class="space-y-3">
                    @foreach($historico as $registro)
                        <div class="relative pl-6 pb-3 {{ !$loop->last ? 'border-l-2 border-gray-200 dark:border-gray-700' : '' }}">
                            <!-- Bolinha -->
                            <div class="absolute left-0 top-0 w-3 h-3 rounded-full -translate-x-1.5
                                {{ $registro->data_saida ? 'bg-gray-400' : 'bg-green-500' }}">
                            </div>

                            <div class="text-sm">
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $registro->ponto->endereco->logradouro ?? 'Ponto' }}, {{ $registro->ponto->numero ?? '' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $registro->ponto->endereco->bairro ?? '' }}
                                </p>
                                <div class="flex items-center gap-4 mt-1 text-xs text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Entrada: {{ $registro->data_entrada->format('d/m/Y') }}
                                    </span>
                                    @if($registro->data_saida)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            Saida: {{ $registro->data_saida->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-green-600 dark:text-green-400 font-medium">Atual</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Nenhum historico de movimentacao registrado.</p>
            @endif
        </div>

        <!-- Acoes -->
        <div class="flex gap-3">
            <a href="{{ route('moradores.edit', $morador) }}"
               class="flex-1 py-3 bg-blue-500 text-white rounded-lg text-center font-medium hover:bg-blue-600 transition">
                Editar
            </a>
            <form action="{{ route('moradores.destroy', $morador) }}" method="POST" class="flex-1"
                  onsubmit="return confirm('Tem certeza que deseja excluir este morador?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-3 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition">
                    Excluir
                </button>
            </form>
        </div>
    </div>
@endsection
