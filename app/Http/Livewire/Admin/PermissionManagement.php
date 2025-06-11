<?php

namespace App\Http\Livewire\Admin;

use App\Models\Permission;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionManagement extends Component
{
    public $permissions;
    public $permissions_by_module = [];
    public $permissionIdToDelete;
    public $confirmingPermissionDeletion = false;
    
    // Pour la création
    public $name;
    public $module;
    public $action;
    public $description;
    
    // Pour l'édition
    public $permissionToEdit;
    public $editingPermission = false;

    protected $rules = [
        'module' => 'required|string|max:30',
        'action' => 'required|string|max:30',
        'description' => 'required|string'
    ];

    protected $listeners = ['refreshPermissions' => '$refresh'];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
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

    public function createPermission()
    {
        $this->validate();

        // Format name as module.action
        $formattedName = Str::slug($this->module, '') . '.' . Str::slug($this->action, '');

        // Check if already exists
        if (Permission::where('name', $formattedName)->exists()) {
            $this->addError('name', 'Cette permission existe déjà');
            return;
        }

        Permission::create([
            'name' => $formattedName,
            'description' => $this->description
        ]);

        $this->resetForm();
        $this->emit('alert', 'success', 'Permission créée avec succès');
        $this->refreshData();
        $this->emit('permissionCreated');
    }

    public function confirmPermissionDeletion($permissionId)
    {
        $this->permissionIdToDelete = $permissionId;
        $this->confirmingPermissionDeletion = true;
    }

    public function deletePermission()
    {
        $permission = Permission::find($this->permissionIdToDelete);
        
        if ($permission) {
            // Vérifier si la permission est associée à des rôles
            if ($permission->roles()->count() > 0) {
                $this->emit('alert', 'error', 'Cette permission est attribuée à des rôles et ne peut pas être supprimée directement.');
            } else {
                $permission->delete();
                $this->emit('alert', 'success', 'Permission supprimée avec succès');
            }
        }

        $this->confirmingPermissionDeletion = false;
        $this->permissionIdToDelete = null;
        $this->refreshData();
    }

    public function editPermission($permissionId)
    {
        $this->resetForm();
        $this->permissionToEdit = Permission::find($permissionId);
        $this->editingPermission = true;

        // Extraire le module et l'action du nom de permission
        $parts = explode('.', $this->permissionToEdit->name);
        $this->module = $parts[0];
        $this->action = $parts[1] ?? '';
        $this->description = $this->permissionToEdit->description;
    }

    public function updatePermission()
    {
        $this->validate();

        // Format name as module.action
        $formattedName = Str::slug($this->module, '') . '.' . Str::slug($this->action, '');

        // Check if already exists (excluding current permission)
        if (Permission::where('name', $formattedName)->where('id', '!=', $this->permissionToEdit->id)->exists()) {
            $this->addError('name', 'Cette permission existe déjà');
            return;
        }

        $this->permissionToEdit->update([
            'name' => $formattedName,
            'description' => $this->description
        ]);

        $this->emit('alert', 'success', 'Permission mise à jour avec succès');
        $this->resetForm();
        $this->refreshData();
        $this->emit('permissionUpdated');
    }

    private function resetForm()
    {
        $this->name = '';
        $this->module = '';
        $this->action = '';
        $this->description = '';
        $this->editingPermission = false;
        $this->permissionToEdit = null;
    }

    public function render()
    {
        return view('livewire.admin.permission-management');
    }
}
