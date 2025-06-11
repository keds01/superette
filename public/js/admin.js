// Fichier JavaScript pour la page d'administration
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin JS loaded!');
    
    // Initialisation des tooltips Bootstrap
    try {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    } catch (e) {
        console.error('Erreur lors de l\'initialisation des tooltips:', e);
    }

    // Initialisation des onglets avec le nouveau design Tailwind
    var tabElms = document.querySelectorAll('[data-bs-toggle="tab"]');
    console.log('Nombre d\'onglets trouvés:', tabElms.length);
    
    tabElms.forEach(function(tabEl) {
        tabEl.addEventListener('click', function(event) {
            event.preventDefault();
            console.log('Onglet cliqué:', this.getAttribute('data-bs-target'));
            
            var targetId = this.getAttribute('data-bs-target');
            var target = document.querySelector(targetId);
            
            if (!target) {
                console.error('Cible introuvable:', targetId);
                return;
            }
            
            // Cacher tous les panneaux
            document.querySelectorAll('.tab-pane').forEach(function(pane) {
                pane.classList.remove('show', 'active');
            });
            
            // Désactiver tous les onglets et les indicateurs
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tab) {
                tab.classList.remove('active');
                tab.classList.remove('text-indigo-600');
                tab.classList.add('text-gray-500');
                tab.setAttribute('aria-selected', 'false');
                
                // Masquer l'indicateur de l'onglet
                var indicator = tab.querySelector('.active-tab-indicator') || tab.nextElementSibling;
                if (indicator && indicator.classList.contains('w-full')) {
                    indicator.classList.remove('scale-100');
                    indicator.classList.add('scale-0');
                }
            });
            
            // Activer l'onglet cliqué et son panneau
            this.classList.add('active');
            this.classList.remove('text-gray-500');
            this.classList.add('text-indigo-600');
            this.setAttribute('aria-selected', 'true');
            
            // Afficher l'indicateur de l'onglet actif
            var activeIndicator = this.querySelector('.w-full') || this.querySelector('div:last-child');
            if (activeIndicator) {
                activeIndicator.classList.remove('scale-0');
                activeIndicator.classList.add('scale-100');
            }
            
            // Afficher le panneau correspondant
            target.classList.add('show', 'active');
            console.log('Panneau activé:', targetId);
        });
    });

    // Gestion des checkboxes "Sélectionner tout" pour les permissions
    var selectAllCheckbox = document.getElementById('select-all-permissions');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var allChecked = true;
                document.querySelectorAll('.permission-checkbox').forEach(function(cb) {
                    if (!cb.checked) allChecked = false;
                });
                selectAllCheckbox.checked = allChecked;
            });
        });
    }

    // Initialisation des modales
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(element) {
        element.addEventListener('click', function() {
            var target = document.querySelector(this.getAttribute('data-bs-target'));
            var modal = new bootstrap.Modal(target);
            modal.show();
        });
    });
    
    // Édition utilisateur
    document.querySelectorAll('.edit-user-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var userId = this.getAttribute('data-user-id');
            var userName = this.getAttribute('data-user-name');
            var userEmail = this.getAttribute('data-user-email');
            var userActive = this.getAttribute('data-user-active');
            
            document.getElementById('editUserForm').setAttribute('action', '/users/' + userId);
            document.getElementById('edit_name').value = userName;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_actif').checked = userActive === '1';
            
            // Charger les rôles de l'utilisateur via fetch API
            fetch('/users/' + userId + '/roles')
                .then(response => response.json())
                .then(data => {
                    var container = document.getElementById('edit_roles_container');
                    container.innerHTML = '';
                    
                    data.roles.forEach(function(role) {
                        var isChecked = data.user_roles.includes(role.id) ? 'checked' : '';
                        
                        var div = document.createElement('div');
                        div.className = 'col-md-4 mb-2';
                        div.innerHTML = `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_role-${role.id}" name="roles[]" value="${role.id}" ${isChecked}>
                                <label class="form-check-label" for="edit_role-${role.id}">
                                    ${role.nom}
                                </label>
                            </div>
                        `;
                        container.appendChild(div);
                    });
                });
        });
    });
    
    // Édition rôle
    document.querySelectorAll('.edit-role-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var roleId = this.getAttribute('data-role-id');
            var roleName = this.getAttribute('data-role-name');
            var roleDescription = this.getAttribute('data-role-description');
            
            document.getElementById('editRoleForm').setAttribute('action', '/roles/' + roleId);
            document.getElementById('edit_role_nom').value = roleName;
            document.getElementById('edit_role_description').value = roleDescription;
            
            // Charger les permissions du rôle via fetch API
            fetch('/roles/' + roleId + '/permissions')
                .then(response => response.json())
                .then(data => {
                    var container = document.getElementById('edit_permissions_container');
                    container.innerHTML = '';
                    
                    // Grouper les permissions par module
                    var permissionsByModule = {};
                    data.permissions.forEach(function(permission) {
                        var module = permission.module || 'Autre';
                        if (!permissionsByModule[module]) {
                            permissionsByModule[module] = [];
                        }
                        permissionsByModule[module].push(permission);
                    });
                    
                    // Créer les cartes de permissions par module
                    Object.keys(permissionsByModule).forEach(function(module) {
                        var div = document.createElement('div');
                        div.className = 'col-md-4 mb-3';
                        
                        var moduleHtml = `
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">${module}</h6>
                                </div>
                                <div class="card-body">
                        `;
                        
                        permissionsByModule[module].forEach(function(permission) {
                            var isChecked = data.role_permissions.includes(permission.id) ? 'checked' : '';
                            moduleHtml += `
                                <div class="form-check">
                                    <input class="form-check-input edit-permission-checkbox" type="checkbox" id="edit_permission-${permission.id}" name="permissions[]" value="${permission.id}" ${isChecked}>
                                    <label class="form-check-label" for="edit_permission-${permission.id}">
                                        ${permission.description}
                                    </label>
                                </div>
                            `;
                        });
                        
                        moduleHtml += `
                                </div>
                            </div>
                        `;
                        
                        div.innerHTML = moduleHtml;
                        container.appendChild(div);
                    });
                    
                    // Initialiser le "Sélectionner tout" pour l'édition
                    var editSelectAllCheckbox = document.getElementById('edit-select-all-permissions');
                    if (editSelectAllCheckbox) {
                        editSelectAllCheckbox.addEventListener('change', function() {
                            document.querySelectorAll('.edit-permission-checkbox').forEach(function(checkbox) {
                                checkbox.checked = editSelectAllCheckbox.checked;
                            });
                        });
                        
                        document.querySelectorAll('.edit-permission-checkbox').forEach(function(checkbox) {
                            checkbox.addEventListener('change', function() {
                                var allChecked = true;
                                document.querySelectorAll('.edit-permission-checkbox').forEach(function(cb) {
                                    if (!cb.checked) allChecked = false;
                                });
                                editSelectAllCheckbox.checked = allChecked;
                            });
                        });
                        
                        // Vérifier si toutes les permissions sont sélectionnées
                        var allChecked = true;
                        document.querySelectorAll('.edit-permission-checkbox').forEach(function(checkbox) {
                            if (!checkbox.checked) allChecked = false;
                        });
                        editSelectAllCheckbox.checked = allChecked;
                    }
                });
        });
    });
});
