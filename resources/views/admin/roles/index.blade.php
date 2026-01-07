@extends('layouts.app')

@section('title', 'Gerenciar Roles')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-semibold flex-1 text-center">Gerenciar Roles</h1>
        <div class="w-10"></div>
    </div>
@endsection

@section('content')
    <div class="h-full overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 transition-colors duration-200">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg text-sm transition-colors duration-200">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg text-sm transition-colors duration-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex gap-2 text-sm">
                <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Permissions</a>
                <span class="text-gray-400 dark:text-gray-500 dark:text-gray-400">|</span>
                <a href="{{ route('admin.users.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Usuários</a>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="bg-blue-500 dark:bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700 text-sm font-medium transition-colors duration-200">
                Nova Role
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 overflow-hidden transition-colors duration-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-200">Nome</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-200">Usuários</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-200">Permissions</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-200">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($roles as $role)
                            <tr class="odd:bg-gray-50 dark:odd:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-200">{{ $role->name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $role->users_count }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $role->permissions->count() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm mr-3">Editar</a>
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Nenhuma role cadastrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
