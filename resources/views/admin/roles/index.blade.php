<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="p-1 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-lg font-semibold">Gerenciar Roles</h1>
            </div>
        </div>
    </x-slot>

    <div class="p-4">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex gap-2 text-sm">
                <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 hover:underline">Permissions</a>
                <span class="text-gray-400">|</span>
                <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline">Usuários</a>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">
                Nova Role
            </a>
        </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nome</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Usuários</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Permissions</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($roles as $role)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $role->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $role->users_count }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $role->permissions->count() }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 hover:underline text-sm mr-3">Editar</a>
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Nenhuma role cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-app-layout>
