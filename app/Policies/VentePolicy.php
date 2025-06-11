<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vente;

class VentePolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('vente.view') || $user->hasRole('admin');
    }

    public function view(User $user, Vente $vente)
    {
        return $user->hasPermissionTo('vente.view') || $user->hasRole('admin');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('vente.create') || $user->hasRole('admin');
    }

    public function update(User $user, Vente $vente)
    {
        return $user->hasPermissionTo('vente.update') || $user->hasRole('admin');
    }

    public function delete(User $user, Vente $vente)
    {
        return $user->hasPermissionTo('vente.delete') || $user->hasRole('admin');
    }
}
