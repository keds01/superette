<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Stock;

class StockPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('stock.view') || $user->hasRole('admin');
    }

    public function view(User $user, Stock $stock)
    {
        return $user->hasPermissionTo('stock.view') || $user->hasRole('admin');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('stock.create') || $user->hasRole('admin');
    }

    public function update(User $user, Stock $stock)
    {
        return $user->hasPermissionTo('stock.update') || $user->hasRole('admin');
    }

    public function delete(User $user, Stock $stock)
    {
        return $user->hasPermissionTo('stock.delete') || $user->hasRole('admin');
    }
}
