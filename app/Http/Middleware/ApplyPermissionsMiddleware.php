<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ApplyPermissionsMiddleware
{
    /**
     * Middleware qui applique automatiquement les permissions basées sur la route actuelle.
     * Format de permission attendu: {module}.{action}
     * Exemple: produit.view, client.create, etc.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
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
        
        // Super admin a toutes les permissions
        if ($request->user()->hasRole('super-admin') || $request->user()->hasRole('super_admin')) {
            return $next($request);
        }
        
        // Obtenir la route actuelle et extraire le module et l'action
        $routeName = Route::currentRouteName();
        
        if (!$routeName) {
            // Si pas de nom de route, autoriser l'accès
            return $next($request);
        }
        
        $parts = explode('.', $routeName);
        
        if (count($parts) < 2) {
            // Si le format n'est pas valide, autoriser l'accès
            return $next($request);
        }
        
        $module = $parts[0];
        $action = end($parts);
        
        // Mapper l'action de la route à une action de permission
        $actionMap = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete'
        ];
        
        $permissionAction = $actionMap[$action] ?? $action;
        $permission = "{$module}.{$permissionAction}";
        
        // Vérifier si l'utilisateur a la permission requise
        if ($request->user()->hasPermissionTo($permission)) {
            return $next($request);
        }
        
        // Journalisation de la tentative d'accès non autorisée
        Log::warning('Tentative d\'accès non autorisé - Permission automatique manquante', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
            'roles_utilisateur' => $request->user()->roles->pluck('name')->toArray(),
            'permissions_utilisateur' => $request->user()->roles->flatMap(function ($role) {
                return $role->permissions->pluck('name');
            })->unique()->values()->toArray(),
            'permission_requise' => $permission,
            'route' => $routeName,
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);
        
        return response()
            ->view('errors.403', [
                'message' => "Vous n'avez pas la permission nécessaire pour accéder à cette ressource.",
                'permission' => $permission
            ], 403);
    }
}
