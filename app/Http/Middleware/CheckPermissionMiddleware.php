<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckPermissionMiddleware
{
    /**
     * Vérifie si l'utilisateur possède au moins une des permissions spécifiées.
     * Plusieurs permissions peuvent être passées séparées par une virgule.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
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

        $permissions = array_map('trim', explode(',', $permission));

        if (empty($permissions)) {
            return $next($request);
        }
        
        // Super admin a toutes les permissions
        if ($request->user()->hasRole('super-admin') || $request->user()->hasRole('super_admin')) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a au moins une des permissions requises
        foreach ($permissions as $perm) {
            if ($request->user()->hasPermissionTo($perm)) {
                return $next($request);
            }
        }

        // Journalisation de la tentative d'accès non autorisée
        Log::warning('Tentative d\'accès non autorisé - Permission requise manquante', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
            'roles_utilisateur' => $request->user()->roles->pluck('name')->toArray(),
            'permissions_utilisateur' => $request->user()->roles->flatMap(function ($role) {
                return $role->permissions->pluck('name');
            })->unique()->values()->toArray(),
            'permissions_requises' => $permissions,
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);

        return response()
            ->view('errors.403', [
                'message' => "Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.",
                'permission' => implode(', ', $permissions)
            ], 403);
    }
}
