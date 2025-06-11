<div>
    <div class="overflow-hidden shadow-2xl ring-1 ring-indigo-200 ring-opacity-40 sm:rounded-2xl bg-white/70 backdrop-blur-xl">
        <table class="min-w-full divide-y divide-indigo-100">
            <thead class="bg-gradient-to-tr from-indigo-100 to-purple-100">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold text-indigo-900 sm:pl-6">#</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Nom</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold text-indigo-900">Email</th>
                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Rôles</th>
                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Statut</th>
                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold text-indigo-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-50 bg-white/80">
                @foreach($users as $user)
                <tr class="hover:bg-indigo-50/50 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-900 sm:pl-6">{{ $user->id }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-indigo-700">{{ $user->name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-indigo-500">{{ $user->email }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                        @foreach($user->roles as $role)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mr-1">
                            {{ $role->nom }}
                        </span>
                        @endforeach
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                        <button wire:click="toggleUserStatus({{ $user->id }})" type="button" 
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $user->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <div class="mr-1 h-2 w-2 rounded-full {{ $user->actif ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            {{ $user->actif ? 'Actif' : 'Inactif' }}
                        </button>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                        <div class="flex justify-center items-center space-x-2">
                            <button wire:click="editUser({{ $user->id }})" type="button" 
                                class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 hover:bg-indigo-200">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="confirmUserDeletion({{ $user->id }})" type="button" 
                                class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteUserModal" wire:model="confirmingUserDeletion">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" wire:click="$set('confirmingUserDeletion', false)"></button>
                </div>
                <div class="modal-body">
                    @if($userIdToDelete)
                    <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                    <p class="text-danger">Cette action est irréversible.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('confirmingUserDeletion', false)">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteUser">Confirmer la suppression</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'édition -->
    <div class="modal fade" id="editUserModal" wire:model="editingUser">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Éditer l'utilisateur</h5>
                    <button type="button" class="btn-close" wire:click="$set('editingUser', false)"></button>
                </div>
                <div class="modal-body">
                    @if($userToEdit)
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" wire:model.defer="userToEdit.name">
                        @error('userToEdit.name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" wire:model.defer="userToEdit.email">
                        @error('userToEdit.email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    @endif
                    {{-- Nouvelle section pour les rôles --}}
                    @if($userToEdit)
                    <div class="mb-3 mt-4">
                        <h5 class="mb-3">Rôles attribués</h5>
                        @foreach($allRoles as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   id="role-{{ $role->id }}" 
                                   value="{{ $role->id }}"
                                   wire:model.defer="userToEditRoles" {{-- Liaison Livewire --}}
                                   {{ in_array($role->id, $userToEditRoles ?? []) ? 'checked' : '' }}> {{-- Vérification si le rôle est déjà attribué --}}
                            <label class="form-check-label" for="role-{{ $role->id }}">
                                {{ $role->nom }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('editingUser', false)">Annuler</button>
                    <button type="button" class="btn btn-primary" wire:click="updateUser">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Ouvrir/fermer le modal d'édition via Livewire
    window.addEventListener('open-edit-modal', event => {
        $('#editUserModal').modal('show');
    });

    window.addEventListener('close-edit-modal', event => {
        $('#editUserModal').modal('hide');
    });

     // Ouvrir/fermer le modal de suppression via Livewire
    window.addEventListener('open-delete-modal', event => {
        $('#deleteUserModal').modal('show');
    });

    window.addEventListener('close-delete-modal', event => {
        $('#deleteUserModal').modal('hide');
    });
});
</script>
@endpush
