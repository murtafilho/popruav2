@extends('layouts.app')

@section('title', 'Nova Permission')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.permissions.index') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-semibold flex-1 text-center">Nova Permission</h1>
        <div class="w-10"></div>
    </div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 transition-colors duration-200">
        <div class="max-w-xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 p-6 transition-colors duration-200">
                <form action="{{ route('admin.permissions.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-200 dark:text-gray-300 mb-1">Nome da Permission *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ex: ver relatorios, criar usuarios, editar vistorias"
                            class="w-full px-4 py-3 text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg bg-[#93a6c2] text-black focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400 placeholder:opacity-70 transition-all duration-200 @error('name') border-red-500 focus:ring-red-500 @enderror"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use letras minúsculas e espaços ou underscores. Ex: ver_relatorios ou ver relatorios</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium transition-colors duration-200">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition-colors duration-200">
                            Criar Permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
