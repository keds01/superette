<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Remise;

class RemisePolicy
{
    public function viewAny(User $user) { return $user->hasPermissionTo('remise.view') || $user->hasRole('admin'); }
    public function view(User $user, Remise $remise) { return $user->hasPermissionTo('remise.view') || $user->hasRole('admin'); }
    public function create(User $user) { return $user->hasPermissionTo('remise.create') || $user->hasRole('admin'); }
    public function update(User $user, Remise $remise) { return $user->hasPermissionTo('remise.update') || $user->hasRole('admin'); }
    public function delete(User $user, Remise $remise) { return $user->hasPermissionTo('remise.delete') || $user->hasRole('admin'); }
}
