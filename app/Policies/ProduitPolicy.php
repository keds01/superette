<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Produit;

class ProduitPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('produit.view') || $user->hasRole('admin');
    }

    public function view(User $user, Produit $produit)
    {
        return $user->hasPermissionTo('produit.view') || $user->hasRole('admin');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('produit.create') || $user->hasRole('admin');
    }

    public function update(User $user, Produit $produit)
    {
        return $user->hasPermissionTo('produit.update') || $user->hasRole('admin');
    }

    public function delete(User $user, Produit $produit)
    {
        return $user->hasPermissionTo('produit.delete') || $user->hasRole('admin');
    }
}
