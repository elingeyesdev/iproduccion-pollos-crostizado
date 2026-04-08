<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Administrar Roles
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Barra superior: buscador + botón nuevo --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <form method="GET" action="{{ route('users.index') }}" class="flex gap-2 w-full sm:w-auto">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Buscar por nombre o email..."
                            class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-72"
                        >
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">
                            Buscar
                        </button>
                        @if(request('search'))
                            <a href="{{ route('users.index') }}"
                               class="px-4 py-2 bg-gray-400 text-white text-sm rounded-md hover:bg-gray-500 transition">
                                Limpiar
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('users.create') }}"
                       class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition whitespace-nowrap">
                        + Nuevo Usuario
                    </a>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-200 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100 font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="inline-block bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 text-xs font-semibold px-2 py-1 rounded-full">
                                            {{ $role }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="px-3 py-1 bg-yellow-500 text-white text-xs rounded-md hover:bg-yellow-600 transition">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                                              onsubmit="return confirm('¿Eliminar este usuario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 bg-red-600 text-white text-xs rounded-md hover:bg-red-700 transition">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400 dark:text-gray-500">
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>