<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Gerenciar Usuários</h1>
        </div>
    </x-slot>

    <div class="p-4">
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex gap-2">
        <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:underline text-sm">Roles</a>
        <span class="text-gray-400">|</span>
        <a href="{{ route('admin.permissions.index') }}" class="text-blue-600 hover:underline text-sm">Permissions</a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-200">Nome</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-200">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-200">Role</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="odd:bg-gray-50 hover:bg-gray-100">
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-600 text-sm">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.users.roles.update', $user) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select
                                    name="role"
                                    class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
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
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">Nenhum usuário cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-app-layout>
