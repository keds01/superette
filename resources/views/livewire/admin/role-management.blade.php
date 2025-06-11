<div>
    <div class="mt-8 flow-root">
        <!-- Table de rôles -->
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-2xl ring-1 ring-indigo-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                    <table class="min-w-full divide-y divide-indigo-100">
                        <thead class="bg-gradient-to-tr from-indigo-100 to-purple-100">
                            <tr>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">ID</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Nom</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Description</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Permissions</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-50 bg-white/80">
                            @foreach($roles as $role)
                            <tr class="hover:bg-indigo-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-900 sm:pl-6">{{ $role->id }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $role->nom }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $role->description }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                                        {{ $role->permissions->count() }} permissions
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('admin.roles.show', $role) }}" class="text-green-600 hover:text-green-900 transition-colors" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button wire:click="editRole({{ $role->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="confirmRoleDeletion({{ $role->id }})" class="text-red-600 hover:text-red-900 transition-colors" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de création de rôle -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="createRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-r from-green-400 to-emerald-500">
                    <h5 class="modal-title text-white" id="createRoleModalLabel">Ajouter un nouveau rôle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="createRole">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du rôle</label>
                            <input wire:model="nom" type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" placeholder="Ex: gestionnaire">
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" id="description" rows="2" placeholder="Description des responsabilités et accès"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="permissions-container">
                                <div class="row">
                                    @foreach($permissions_by_module as $module => $module_permissions)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">{{ $module }}</h6>
                                            </div>
                                            <div class="card-body">
                                                @foreach($module_permissions as $permission)
                                                <div class="form-check">
                                                    <input wire:model="selectedPermissions" class="form-check-input" type="checkbox" id="permission-{{ $permission->id }}" value="{{ $permission->id }}">
                                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                        {{ $permission->description }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de rôle -->
    <div class="modal fade" id="editRoleModal" wire:ignore.self tabindex="-1" data-bs-backdrop="static" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-r from-blue-400 to-blue-600">
                    <h5 class="modal-title text-white" id="editRoleModalLabel">Modifier le rôle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($editingRole && $roleToEdit)
                    <form wire:submit.prevent="updateRole">
                        <div class="mb-3">
                            <label for="edit_nom" class="form-label">Nom du rôle</label>
                            <input wire:model="nom" type="text" class="form-control @error('nom') is-invalid @enderror" id="edit_nom">
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" id="edit_description" rows="2"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="permissions-container">
                                <div class="row">
                                    @foreach($permissions_by_module as $module => $module_permissions)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">{{ $module }}</h6>
                                            </div>
                                            <div class="card-body">
                                                @foreach($module_permissions as $permission)
                                                <div class="form-check">
                                                    <input wire:model="roleToEditPermissions" class="form-check-input" type="checkbox" id="edit_permission-{{ $permission->id }}" value="{{ $permission->id }}">
                                                    <label class="form-check-label" for="edit_permission-{{ $permission->id }}">
                                                        {{ $permission->description }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteRoleModal" wire:ignore.self tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-r from-red-400 to-red-600">
                    <h5 class="modal-title text-white" id="deleteRoleModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce rôle ? Cette action est irréversible.</p>
                    <p class="text-danger"><small>Note: La suppression échouera si des utilisateurs sont toujours associés à ce rôle.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteRole">
                        <i class="fas fa-trash me-1"></i> Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('roleCreated', function () {
                $('#createRoleModal').modal('hide');
            });
            
            Livewire.on('roleUpdated', function () {
                $('#editRoleModal').modal('hide');
            });
            
            Livewire.on('alert', function (type, message) {
                // Implémentation des alertes (toasts, etc.)
                console.log(type, message);
            });
        });
        
        window.addEventListener('show-edit-role-modal', event => {
            $('#editRoleModal').modal('show');
        });
        
        window.addEventListener('show-delete-role-modal', event => {
            $('#deleteRoleModal').modal('show');
        });
    </script>
    @endpush
</div>
