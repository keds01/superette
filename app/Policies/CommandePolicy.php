<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Commande;

class CommandePolicy
{
    public function viewAny(User $user) { return $user->hasPermissionTo('commande.view') || $user->hasRole('admin'); }
    public function view(User $user, Commande $commande) { return $user->hasPermissionTo('commande.view') || $user->hasRole('admin'); }
    public function create(User $user) { return $user->hasPermissionTo('commande.create') || $user->hasRole('admin'); }
    public function update(User $user, Commande $commande) { return $user->hasPermissionTo('commande.update') || $user->hasRole('admin'); }
    public function delete(User $user, Commande $commande) { return $user->hasPermissionTo('commande.delete') || $user->hasRole('admin'); }
}
