<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie si l'utilisateur a au moins l'un des rôles spécifiés
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Si l'utilisateur n'est pas connecté, redirection vers la page de connexion
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        // Préparation des rôles à vérifier (séparés par des virgules)
        $roles = array_map('trim', explode(',', $role));
        
        // Si aucun rôle n'est requis, on continue
        if (empty($roles)) {
            return $next($request);
        }

        // Vérification si l'utilisateur a au moins l'un des rôles spécifiés
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // Si l'utilisateur n'a pas le rôle requis, redirection avec message d'erreur
        return redirect()->route('dashboard')
            ->with('error', 'Accès refusé : vous n\'avez pas les autorisations nécessaires.');
    }
}
