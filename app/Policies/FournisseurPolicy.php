<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Fournisseur;

class FournisseurPolicy
{
    public function viewAny(User $user) { return $user->hasPermissionTo('fournisseur.view') || $user->hasRole('admin'); }
    public function view(User $user, Fournisseur $fournisseur) { return $user->hasPermissionTo('fournisseur.view') || $user->hasRole('admin'); }
    public function create(User $user) { return $user->hasPermissionTo('fournisseur.create') || $user->hasRole('admin'); }
    public function update(User $user, Fournisseur $fournisseur) { return $user->hasPermissionTo('fournisseur.update') || $user->hasRole('admin'); }
    public function delete(User $user, Fournisseur $fournisseur) { return $user->hasPermissionTo('fournisseur.delete') || $user->hasRole('admin'); }
}
