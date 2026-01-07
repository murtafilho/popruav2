@extends('layouts.app')

@section('title', 'Gerenciar Usuários')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.roles.index') }}" class="p-2 -ml-2 rounded-lg hover:bg-white/10 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-semibold flex-1 text-center">Gerenciar Usuários</h1>
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
                <a href="{{ route('admin.roles.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Roles</a>
                <span class="text-gray-400 dark:text-gray-500">|</span>
                <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Permissions</a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm dark:shadow-gray-900/50 overflow-hidden transition-colors duration-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-200">Nome</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-200">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-200">Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr class="odd:bg-gray-50 dark:odd:bg-gray-700/30 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-200">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-sm">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.users.roles.update', $user) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select
                                            name="role"
                                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                            onchange="this.form.submit()"
                                        >
                                            <option value="">Sem role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Nenhum usuário cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
