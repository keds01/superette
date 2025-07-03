<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Si aucun rôle n'est requis, laisser passer
        if (empty($roles)) {
            return $next($request);
        }
        
        // Super admin a accès à tout
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        
        // Vérifier si l'utilisateur a l'un des rôles requis
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
        return $next($request);
            }
        }
        
        // Si l'utilisateur n'a pas les permissions requises
        abort(403, 'Accès non autorisé. Vous n\'avez pas les permissions requises.');
    }
}
