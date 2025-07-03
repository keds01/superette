<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SuperetteScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // Ne pas appliquer le scope si l'utilisateur n'est pas connecté
        if (!Auth::check()) {
            return;
        }

        $superetteId = session('active_superette_id');
        $user = Auth::user();

        // Si l'utilisateur est un super admin et qu'aucune superette n'est sélectionnée, ne pas filtrer
        if ($user->isSuperAdmin() && !$superetteId) {
            return;
        }

        // Si l'utilisateur est un employé et n'a pas de superette sélectionnée, utiliser sa superette par défaut
        if (!$superetteId && $user->superette_id) {
            $superetteId = $user->superette_id;
            session(['active_superette_id' => $superetteId]);
        }

        if ($superetteId) {
            $builder->where($model->getTable() . '.superette_id', $superetteId);
        }
    }
} 