<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ApplyPermissions
{
    /**
     * Middleware qui vérifie si l'utilisateur a TOUTES les permissions spécifiées.
     * Contrairement à CheckPermission qui vérifie si l'utilisateur a AU MOINS UNE des permissions.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permissions = null): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        // Vérifier si le compte est actif
        if (!$request->user()->actif) {
            Log::warning('Tentative d\'accès avec un compte désactivé', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);
            
            return response()
                ->view('errors.403', [
                    'message' => "Votre compte a été désactivé. Veuillez contacter l'administrateur.",
                    'permission' => null
                ], 403);
        }
        
        // Si aucune permission n'est spécifiée, continuer
        if (!$permissions) {
            return $next($request);
        }
        
        // Super admin a toutes les permissions
        if ($request->user()->hasRole('super-admin') || $request->user()->hasRole('super_admin')) {
            return $next($request);
        }
        
        // Convertir la chaîne de permissions en tableau
        $permissionArray = array_map('trim', explode(',', $permissions));
        
        // Vérifier si l'utilisateur a TOUTES les permissions requises
        if ($request->user()->hasAllPermissions($permissionArray)) {
            return $next($request);
        }
        
        // Journalisation de la tentative d'accès non autorisée
        Log::warning('Tentative d\'accès non autorisé - Permissions requises manquantes', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
            'roles_utilisateur' => $request->user()->roles->pluck('name')->toArray(),
            'permissions_utilisateur' => $request->user()->roles->flatMap(function ($role) {
                return $role->permissions->pluck('name');
            })->unique()->values()->toArray(),
            'permissions_requises' => $permissionArray,
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);
        
        return response()
            ->view('errors.403', [
                'message' => "Vous n'avez pas toutes les permissions nécessaires pour accéder à cette ressource.",
                'permission' => implode(', ', $permissionArray)
            ], 403);
    }
}
