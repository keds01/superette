@extends('layouts.app')

@section('title', 'Gestion des rôles et permissions')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-blue-600 via-indigo-500 to-purple-400 bg-clip-text text-transparent tracking-tight drop-shadow-lg">
                    Gestion des rôles et permissions
                </h1>
                <p class="mt-2 text-lg text-gray-500">Gérez les rôles et les permissions des utilisateurs du système.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <button onclick="document.getElementById('createRoleModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-400 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    Nouveau rôle
                </button>
            </div>
        </div>

        @if (session('success'))
        <div class="mb-6">
            <div class="rounded-xl bg-gradient-to-tr from-green-100 via-green-50 to-white border border-green-200 px-6 py-4 text-green-800 font-semibold shadow-lg flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
            </div>
        </div>
        @endif

        @if (session('error'))
        <div class="mb-6">
            <div class="rounded-xl bg-gradient-to-tr from-red-100 via-red-50 to-white border border-red-200 px-6 py-4 text-red-800 font-semibold shadow-lg flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
            </div>
        </div>
        @endif

        <!-- Tableau glassy -->
        <div class="overflow-x-auto rounded-2xl shadow-2xl bg-white/70 backdrop-blur-xl border border-blue-100">
            <table class="min-w-full divide-y divide-blue-200">
                <thead class="bg-gradient-to-tr from-blue-100 to-indigo-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Permissions</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white/80 divide-y divide-blue-50">
                    @foreach ($roles as $role)
                    <tr class="hover:bg-blue-50/70 transition-all">
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-blue-600">{{ $role->nom }}</td>
                        <td class="px-6 py-4">{{ $role->description }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach ($role->permissions as $permission)
                                <span class="inline-block px-3 py-1 rounded-full bg-gradient-to-tr from-blue-200 to-indigo-100 text-blue-800 text-xs font-bold mr-2">
                                    {{ $permission->nom }}
                                </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <a href="{{ route('roles.show', $role->id) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 hover:bg-blue-200 text-blue-600 transition"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('roles.edit', $role->id) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 hover:bg-indigo-200 text-indigo-600 transition" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if ($role->users->isEmpty())
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 hover:bg-red-200 text-red-600 transition" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de création de rôle -->
<div id="createRoleModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom du rôle</label>
                        <input type="text" name="nom" id="nom" required
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Permissions</label>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach ($permissions as $permission)
                                <div class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">
                                        {{ $permission->nom }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Créer
                    </button>
                    <button type="button" onclick="document.getElementById('createRoleModal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de modification de rôle -->
<div id="editRoleModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <label for="edit_nom" class="block text-sm font-medium text-gray-700">Nom du rôle</label>
                        <input type="text" name="nom" id="edit_nom" required
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="edit_description" rows="3"
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Permissions</label>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach ($permissions as $permission)
                                <div class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                           class="edit-permission h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">
                                        {{ $permission->nom }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Mettre à jour
                    </button>
                    <button type="button" onclick="document.getElementById('editRoleModal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editRole(id, nom, description, permissions) {
        const modal = document.getElementById('editRoleModal');
        const form = document.getElementById('editRoleForm');
        const nomInput = document.getElementById('edit_nom');
        const descriptionInput = document.getElementById('edit_description');
        const permissionInputs = document.querySelectorAll('.edit-permission');

        form.action = `/roles/${id}`;
        nomInput.value = nom;
        descriptionInput.value = description;

        permissionInputs.forEach(input => {
            input.checked = permissions.includes(parseInt(input.value));
        });

        modal.classList.remove('hidden');
    }
</script>
@endpush
@endsection