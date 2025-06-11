<?php

namespace App\Http\Livewire\Admin;

use App\Models\Role;
use App\Models\Permission;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class RoleManagement extends Component
{
    public $roles;
    public $permissions;
    public $permissions_by_module;
    public $roleIdToDelete;
    public $confirmingRoleDeletion = false;
    
    // Pour la création
    public $nom;
    public $description;
    public $selectedPermissions = [];
    
    // Pour l'édition
    public $roleToEdit;
    public $editingRole = false;
    public $roleToEditPermissions = [];

    protected $rules = [
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string',
        'selectedPermissions' => 'array',
    ];

    protected $listeners = ['refreshRoles' => '$refresh'];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->roles = Role::with('permissions')->get();
        $this->permissions = Permission::orderBy('name')->get();
        
        // Regrouper les permissions par module
        $this->permissions_by_module = [];
        foreach ($this->permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = ucfirst($parts[0]);
            
            if (!isset($this->permissions_by_module[$module])) {
                $this->permissions_by_module[$module] = [];
            }
            
            $this->permissions_by_module[$module][] = $permission;
        }
    }

    public function createRole()
    {
        $this->validate();

        DB::transaction(function () {
            $role = Role::create([
                'nom' => $this->nom,
                'description' => $this->description
            ]);

            $role->permissions()->attach($this->selectedPermissions);
        });

        $this->resetForm();
        $this->emit('alert', 'success', 'Rôle créé avec succès');
        $this->refreshData();
        $this->emit('roleCreated');
    }

    public function confirmRoleDeletion($roleId)
    {
        $this->roleIdToDelete = $roleId;
        $this->confirmingRoleDeletion = true;
    }

    public function deleteRole()
    {
        $role = Role::find($this->roleIdToDelete);
        
        if ($role) {
            if ($role->users()->count() > 0) {
                $this->emit('alert', 'error', 'Ce rôle est attribué à des utilisateurs et ne peut pas être supprimé.');
            } else {
                $role->permissions()->detach();
                $role->delete();
                $this->emit('alert', 'success', 'Rôle supprimé avec succès');
            }
        }

        $this->confirmingRoleDeletion = false;
        $this->roleIdToDelete = null;
        $this->refreshData();
    }

    public function editRole($roleId)
    {
        $this->resetForm();
        $this->roleToEdit = Role::with('permissions')->find($roleId);
        $this->editingRole = true;
        $this->nom = $this->roleToEdit->nom;
        $this->description = $this->roleToEdit->description;
        $this->roleToEditPermissions = $this->roleToEdit->permissions->pluck('id')->toArray();
    }

    public function updateRole()
    {
        $this->validate([
            'nom' => 'required|string|max:255|unique:roles,nom,' . $this->roleToEdit->id,
            'description' => 'nullable|string',
            'roleToEditPermissions' => 'nullable|array',
        ]);

        DB::transaction(function () {
            $this->roleToEdit->update([
                'nom' => $this->nom,
                'description' => $this->description
            ]);
            
            $this->roleToEdit->permissions()->sync($this->roleToEditPermissions);
        });

        $this->emit('alert', 'success', 'Rôle mis à jour avec succès');
        $this->resetForm();
        $this->refreshData();
        $this->emit('roleUpdated');
    }

    private function resetForm()
    {
        $this->nom = '';
        $this->description = '';
        $this->selectedPermissions = [];
        $this->editingRole = false;
        $this->roleToEdit = null;
        $this->roleToEditPermissions = [];
    }

    public function render()
    {
        return view('livewire.admin.role-management');
    }
}
