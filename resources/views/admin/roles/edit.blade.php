<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Editar Role: {{ $role->name }}</h1>
        </div>
    </x-slot>

    <div class="p-4">
        <div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="bg-white rounded shadow p-6">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome da Role</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $role->name) }}"
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror"
                required
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-80 overflow-y-auto border border-gray-200 rounded p-3">
                @foreach($permissions as $permission)
                    <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->id }}"
                            {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary focus:ring-primary"
                        >
                        <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:bg-blue-600">
                Salvar Alterações
            </button>
        </div>
        </form>
        </div>
    </div>
</x-app-layout>
