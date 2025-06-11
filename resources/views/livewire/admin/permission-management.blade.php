<div>
    <div class="mt-8 flow-root">
        <!-- Table de permissions -->
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-2xl ring-1 ring-purple-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
                    <table class="min-w-full divide-y divide-purple-100">
                        <thead class="bg-gradient-to-tr from-purple-100 to-indigo-100">
                            <tr>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-purple-900">ID</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-purple-900">Nom</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-purple-900">Description</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-purple-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-50 bg-white/80">
                            @foreach($permissions as $permission)
                            <tr class="hover:bg-purple-50/50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-purple-900 sm:pl-6">{{ $permission->id }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700 font-mono">{{ $permission->nom }}</td>
                                <td class="px-3 py-4 text-sm text-gray-700">{{ $permission->description }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button wire:click="editPermission({{ $permission->id }})" class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="confirmPermissionDeletion({{ $permission->id }})" class="text-red-600 hover:text-red-900 transition-colors">
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
        
        <!-- Résumé par module -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Permissions par module</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($permissions_by_module as $module => $module_permissions)
                <div class="bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-lg p-4">
                    <h4 class="text-md font-bold text-purple-900 mb-2">{{ $module }}</h4>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($module_permissions as $permission)
                        <li class="text-sm text-gray-700">{{ $permission->description }}</li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bouton pour ajouter une permission -->
    <div class="flex justify-end mt-6">
        <button data-bs-toggle="modal" data-bs-target="#createPermissionModal" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-500 text-white font-bold shadow-lg hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
            <i class="fas fa-plus-circle"></i>
            Ajouter une permission
        </button>
    </div>

    <!-- Modal de création de permission -->
    <div class="modal fade" id="createPermissionModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-r from-purple-400 to-indigo-500">
                    <h5 class="modal-title text-white" id="createPermissionModalLabel">Ajouter une nouvelle permission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="createPermission">
                        <div class="mb-3">
                            <label for="module" class="form-label">Module</label>
                            <input wire:model="module" type="text" class="form-control @error('module') is-invalid @enderror" id="module" placeholder="Ex: produits">
                            <small class="text-muted">Le nom du module fonctionnel (produits, ventes, etc.)</small>
                            @error('module') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="action" class="form-label">Action</label>
                            <input wire:model="action" type="text" class="form-control @error('action') is-invalid @enderror" id="action" placeholder="Ex: creer">
                            <small class="text-muted">L'action associée (voir, creer, modifier, supprimer, etc.)</small>
                            @error('action') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" id="description" rows="2" placeholder="Ex: Permet de créer un nouveau produit"></textarea>
                            <small class="text-muted">Description claire de ce que permet cette permission</small>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <strong>Aperçu:</strong> 
                                <code>{{ $module ? strtolower($module) : 'module' }}.{{ $action ? strtolower($action) : 'action' }}</code>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de permission -->
    <div class="modal fade" id="editPermissionModal" wire:ignore.self tabindex="-1" data-bs-backdrop="static" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-r from-indigo-400 to-blue-600">
                    <h5 class="modal-title text-white" id="editPermissionModalLabel">Modifier la permission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($editingPermission && $permissionToEdit)
                    <form wire:submit.prevent="updatePermission">
                        <div class="mb-3">
                            <label for="edit_module" class="form-label">Module</label>
                            <input wire:model="module" type="text" class="form-control @error('module') is-invalid @enderror" id="edit_module">
                            <small class="text-muted">Le nom du module fonctionnel</small>
                            @error('module') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_action" class="form-label">Action</label>
                            <input wire:model="action" type="text" class="form-control @error('action') is-invalid @enderror" id="edit_action">
                            <small class="text-muted">L'action associée</small>
                            @error('action') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" id="edit_description" rows="2"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <strong>Aperçu:</strong> 
                                <code>{{ $module ? strtolower($module) : 'module' }}.{{ $action ? strtolower($action) : 'action' }}</code>
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
    <div class="modal fade" id="deletePermissionModal" wire:ignore.self tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-gradient-to-r from-red-400 to-red-600">
                    <h5 class="modal-title text-white" id="deletePermissionModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cette permission ? Cette action est irréversible.</p>
                    <p class="text-danger"><small>Note: La suppression échouera si des rôles utilisent toujours cette permission.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deletePermission">
                        <i class="fas fa-trash me-1"></i> Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('permissionCreated', function () {
                $('#createPermissionModal').modal('hide');
            });
            
            Livewire.on('permissionUpdated', function () {
                $('#editPermissionModal').modal('hide');
            });
            
            Livewire.on('alert', function (type, message) {
                // Implémentation des alertes (toasts, etc.)
                console.log(type, message);
            });
        });
        
        window.addEventListener('show-edit-permission-modal', event => {
            $('#editPermissionModal').modal('show');
        });
        
        window.addEventListener('show-delete-permission-modal', event => {
            $('#deletePermissionModal').modal('show');
        });
    </script>
    @endpush
</div>
