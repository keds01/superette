@extends('layouts.app')

@section('title', 'Administration du système')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">Administration du système</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header moderne -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10 animate-fade-in-down">
            <div>
                <h1 class="text-4xl font-extrabold bg-gradient-to-tr from-indigo-600 via-blue-500 to-purple-600 bg-clip-text text-transparent tracking-tight drop-shadow-lg">Administration du Système</h1>
                <p class="mt-2 text-lg text-gray-500">Gérez les utilisateurs, les rôles et les permissions du système</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <button data-bs-toggle="modal" data-bs-target="#createUserModal" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Ajouter un utilisateur
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="relative bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in-down" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-green-500"></i>
                <span class="font-medium">{{ session('success') }}</span>
                <button type="button" class="ml-auto text-green-500 hover:text-green-800" data-bs-dismiss="alert" aria-label="Close">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="relative bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md animate-fade-in-down" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                <span class="font-medium">{{ session('error') }}</span>
                <button type="button" class="ml-auto text-red-500 hover:text-red-800" data-bs-dismiss="alert" aria-label="Close">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        </div>
        @endif

        <!-- Statistiques glassmorphism -->
        <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
            <div class="relative bg-white/60 backdrop-blur-xl border border-indigo-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <h3 class="text-sm font-medium text-indigo-700 mt-3">Total utilisateurs</h3>
                <p class="text-3xl font-bold text-indigo-900 mt-2">{{ $users->count() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-green-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <i class="fas fa-user-tag text-white text-2xl"></i>
                </div>
                <h3 class="text-sm font-medium text-green-700 mt-3">Total rôles</h3>
                <p class="text-3xl font-bold text-green-900 mt-2">{{ $roles->count() }}</p>
            </div>
            <div class="relative bg-white/60 backdrop-blur-xl border border-purple-100 rounded-2xl shadow-2xl p-6 flex flex-col items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <i class="fas fa-lock text-white text-2xl"></i>
                </div>
                <h3 class="text-sm font-medium text-purple-700 mt-3">Total permissions</h3>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ App\Models\Permission::count() }}</p>
            </div>
        </div>

        <!-- Navigation des sections admin -->
        <!-- Lien unique vers la gestion des utilisateurs -->
        <div class="mb-8">
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 text-white font-bold shadow-xl hover:shadow-neon hover:-translate-y-1 transition-all duration-200">
                <i class="fas fa-users"></i>
                Gérer les utilisateurs
            </a>
        </div>
        <!-- Fin du lien nav utilisateurs -->
        


<!-- Modal Créer Utilisateur -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Ajouter un nouvel utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" checked>
                            <label class="form-check-label" for="actif">Compte actif</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rôles</label>
                        <div class="row">
                            @foreach($roles as $role)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}">
                                    <label class="form-check-label" for="role-{{ $role->id }}">
                                        {{ $role->nom }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éditer Utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editUserForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Modifier l'utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_password" class="form-label">Nouveau mot de passe (laisser vide pour conserver l'actuel)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_actif" name="actif" value="1">
                            <label class="form-check-label" for="edit_actif">Compte actif</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rôles</label>
                        <div class="row" id="edit_roles_container">
                            <!-- Les rôles seront ajoutés dynamiquement ici -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Créer Rôle -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createRoleModalLabel">Ajouter un nouveau rôle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="role_nom" class="form-label">Nom du rôle</label>
                            <input type="text" class="form-control" id="role_nom" name="nom" required>
                        </div>
                        <div class="col-md-6">
                            <label for="role_description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="role_description" name="description" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all-permissions">
                                <label class="form-check-label fw-bold" for="select-all-permissions">
                                    Sélectionner toutes les permissions
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            @foreach($permissions_by_module as $module => $perms)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">{{ $module }}</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($perms as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox" id="permission-{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}">
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
                    <button type="submit" class="btn btn-primary">Créer le rôle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éditer Rôle -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editRoleForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Modifier le rôle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_role_nom" class="form-label">Nom du rôle</label>
                            <input type="text" class="form-control" id="edit_role_nom" name="nom" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_role_description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="edit_role_description" name="description" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit-select-all-permissions">
                                <label class="form-check-label fw-bold" for="edit-select-all-permissions">
                                    Sélectionner toutes les permissions
                                </label>
                            </div>
                        </div>
                        <div class="row" id="edit_permissions_container">
                            <!-- Les permissions seront ajoutées dynamiquement ici -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Créer Utilisateur -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Ajouter un nouvel utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_name" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="user_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="user_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="user_email" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="user_password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="user_password_confirmation" class="form-label">Confirmation du mot de passe</label>
                            <input type="password" class="form-control" id="user_password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rôles</label>
                        <div class="row">
                            @foreach($roles as $role)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}">
                                    <label class="form-check-label" for="role-{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Alpine.js pour la gestion réactive des onglets -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
// Script pour initialiser les modales Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script admin chargé');
    
    // Initialisation des modales Bootstrap
    var modalElList = [].slice.call(document.querySelectorAll('.modal'));
    var modals = modalElList.map(function (modalEl) {
            });
                    rolesContainer.innerHTML = '';
                    
                    // Créer les cases à cocher pour chaque rôle
                    roles.forEach(role => {
                        const checkbox = document.createElement('div');
                        checkbox.className = 'form-check mb-2';
                        checkbox.innerHTML = `
                            <input type="checkbox" class="form-check-input" name="roles[]" 
                                value="${role.id}" id="role-${role.id}" 
                                ${userRoles.includes(role.id) ? 'checked' : ''}>
                            <label class="form-check-label" for="role-${role.id}">
                                ${role.nom}
                            </label>
                        `;
                        rolesContainer.appendChild(checkbox);
                    });
                })
                .catch(error => console.error('Erreur lors du chargement des rôles:', error));
        });
    });

    // Gestion du changement de statut d'un utilisateur
    document.querySelectorAll('.toggle-user-status-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var userId = this.getAttribute('data-user-id');
            
            // Confirmer le changement de statut
            if (confirm('Êtes-vous sûr de vouloir changer le statut de cet utilisateur ?')) {
                fetch(`/admin/users/${userId}/toggle-status`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Mettre à jour l'interface
                    const statusBtn = document.querySelector(`[data-user-id="${userId}"]`);
                    if (data.active) {
                        statusBtn.classList.remove('bg-red-100', 'text-red-600');
                        statusBtn.classList.add('bg-green-100', 'text-green-600');
                        statusBtn.querySelector('div').classList.remove('bg-red-500');
                        statusBtn.querySelector('div').classList.add('bg-green-500');
                        statusBtn.innerHTML = `
                            <div class="mr-1 h-2 w-2 rounded-full bg-green-500"></div>
                            Actif
                        `;
                    } else {
                        statusBtn.classList.remove('bg-green-100', 'text-green-600');
                        statusBtn.classList.add('bg-red-100', 'text-red-600');
                        statusBtn.querySelector('div').classList.remove('bg-green-500');
                        statusBtn.querySelector('div').classList.add('bg-red-500');
                        statusBtn.innerHTML = `
                            <div class="mr-1 h-2 w-2 rounded-full bg-red-500"></div>
                            Inactif
                        `;
                    }
                    
                    showSuccessMessage('Statut mis à jour avec succès');
                    cleanupModal(document.querySelector('.modal.show'));
                })
                .catch(error => {
                    console.error('Erreur lors du changement de statut:', error);
                    cleanupModal(document.querySelector('.modal.show'));
                });
            }
        });
    });

    // Fonction pour nettoyer une modale
    function cleanupModal(modalElement, reload = false) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
            modalElement.addEventListener('hidden.bs.modal', function() {
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                if (reload) {
                    window.location.reload();
                }
            }, { once: true });
        } else {
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            if (reload) {
                window.location.reload();
            }
        }
    }

    // Fonction pour afficher un message de succès
    function showSuccessMessage(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show fixed-top w-100 m-3';
        alert.innerHTML = `
            <strong>Succès!</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3000);
    }

    // Gestion de l'édition des permissions d'un rôle
    document.querySelectorAll('.edit-role-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            console.log('Bouton édition rôle cliqué');
            var roleId = this.getAttribute('data-role-id');
            var roleName = this.getAttribute('data-role-name');
            var roleDescription = this.getAttribute('data-role-description');
            
            document.getElementById('editRoleForm').setAttribute('action', '/roles/' + roleId);
            document.getElementById('edit_role_nom').value = roleName;
            document.getElementById('edit_role_description').value = roleDescription;
            
            // Charger les permissions
            fetch('/roles/' + roleId + '/permissions')
                .then(response => response.json())
                .then(data => {
                    var container = document.getElementById('edit_permissions_container');
                    if (container) {
                        container.innerHTML = '';
                        
                        // Grouper par module
                        var permissionsByModule = {};
                        data.permissions.forEach(function(permission) {
                            var parts = permission.nom.split('.');
                            var module = parts[0].charAt(0).toUpperCase() + parts[0].slice(1);
                            
                            if (!permissionsByModule[module]) {
                                permissionsByModule[module] = [];
                            }
                            permissionsByModule[module].push(permission);
                        });
                        
                        // Créer les groupes de permissions
                        Object.keys(permissionsByModule).forEach(function(module) {
                            var moduleDiv = document.createElement('div');
                            moduleDiv.className = 'col-md-4 mb-3';
                            
                            var card = document.createElement('div');
                            card.className = 'card h-100';
                            card.innerHTML = `
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">${module}</h6>
                                </div>
                                <div class="card-body">
                                </div>
                            `;
                            
                            var cardBody = card.querySelector('.card-body');
                            
                            permissionsByModule[module].forEach(function(permission) {
                                var isChecked = data.role_permissions.includes(permission.id) ? 'checked' : '';
                                
                                var permDiv = document.createElement('div');
                                permDiv.className = 'form-check';
                                permDiv.innerHTML = `
                                    <input class="form-check-input edit-permission-checkbox" type="checkbox" id="edit_permission-${permission.id}" name="permissions[]" value="${permission.id}" ${isChecked}>
                                    <label class="form-check-label" for="edit_permission-${permission.id}">
                                        ${permission.description}
                                    </label>
                                `;
                                
                                cardBody.appendChild(permDiv);
                            });
                            
                            moduleDiv.appendChild(card);
                            container.appendChild(moduleDiv);
                        });
                    }
                })
                .catch(error => console.error('Erreur lors du chargement des permissions:', error));
        });
    });
    
    // Initialiser les autres éléments dynamiques au chargement du DOM
    console.log('Script chargé avec succès');
    
    // Ajouter un écouteur d'événements pour le bouton "Ajouter un rôle"
    document.querySelectorAll('[data-bs-target="#createRoleModal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            console.log('Ouverture de la modale pour ajouter un rôle');
        });
    });
    
    // Ajouter un écouteur d'événements pour le bouton "Ajouter un utilisateur"
    document.querySelectorAll('[data-bs-target="#createUserModal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            console.log('Ouverture de la modale pour ajouter un utilisateur');
        });
    });
});
</script>
@endpush
