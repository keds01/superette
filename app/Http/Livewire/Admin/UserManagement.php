<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    public $users;
    public $roles;
    public $userIdToDelete;
    public $confirmingUserDeletion = false;
    public $userToEdit;
    public $editingUser = false;
    public $userToEditRoles = [];

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->users = User::with('roles')->get();
        $this->roles = Role::all();
    }

    public function confirmUserDeletion($userId)
    {
        $this->userIdToDelete = $userId;
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser()
    {
        $user = User::find($this->userIdToDelete);
        
        if ($user) {
            $user->delete();
            $this->emit('alert', 'success', 'Utilisateur supprimé avec succès');
        }

        $this->confirmingUserDeletion = false;
        $this->userIdToDelete = null;
        $this->refreshData();
    }

    public function toggleUserStatus($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->actif = !$user->actif;
            $user->save();
            
            $this->emit('alert', 'success', 'Statut mis à jour avec succès');
            $this->refreshData();
        }
    }

    public function editUser($userId)
    {
        $this->userToEdit = User::with('roles')->find($userId);
        $this->editingUser = true;
        $this->userToEditRoles = $this->userToEdit->roles->pluck('id')->toArray();
    }

    public function updateUser()
    {
        $this->validate([
            'userToEdit.name' => 'required|string|max:255',
            'userToEdit.email' => 'required|email|unique:users,email,' . $this->userToEdit->id,
            'userToEditRoles' => 'nullable|array',
            'userToEditRoles.*' => 'exists:roles,id',
        ]);

        $this->userToEdit->save();
        $this->userToEdit->roles()->sync($this->userToEditRoles);
        $this->emit('alert', 'success', 'Utilisateur mis à jour avec succès');
        
        $this->editingUser = false;
        $this->userToEdit = null;
        $this->userToEditRoles = [];
        $this->refreshData();
    }

    public function render()
    {
        return view('livewire.admin.user-management');
    }
}
