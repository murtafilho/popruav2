<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Gerenciar Permissions</h1>
        </div>
    </x-slot>

    <div class="p-4">
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2">
            <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:underline text-sm">Roles</a>
            <span class="text-gray-400">|</span>
            <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline text-sm">Usuários</a>
        </div>
        <a href="{{ route('admin.permissions.create') }}" class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-600">
            Nova Permission
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nome</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Roles</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($permissions as $permission)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $permission->name }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @if($permission->roles->count() > 0)
                                {{ $permission->roles->pluck('name')->join(', ') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">Nenhuma permission cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-app-layout>
