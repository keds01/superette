<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user) { return $user->hasPermissionTo('user.view') || $user->hasRole('admin'); }
    public function view(User $user, User $model) { return $user->hasPermissionTo('user.view') || $user->hasRole('admin') || $user->id === $model->id; }
    public function create(User $user) { return $user->hasPermissionTo('user.create') || $user->hasRole('admin'); }
    public function update(User $user, User $model) { return $user->hasPermissionTo('user.update') || $user->hasRole('admin') || $user->id === $model->id; }
    public function delete(User $user, User $model) { return $user->hasPermissionTo('user.delete') || $user->hasRole('admin'); }
}
