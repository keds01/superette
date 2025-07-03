<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictCaissierAccess
{
    /**
     * Liste des routes auxquelles les caissiers ont accès
     */
    protected $allowedRoutes = [
        'ventes.index',
        'ventes.create',
        'ventes.store',
        'ventes.show',
        'ventes.express',
        'ventes.facture',
        'ventes.recu',
        'clients.index',
        'clients.create',
        'clients.store',
        'clients.show',
        'clients.edit',
        'clients.update',
        'stocks.index',
        'produits.index',
        'produits.show',
        'logout',
        'dashboard',
        'profile.edit',
        'profile.update',
        'profile.destroy',
        'superettes.select',
        'superettes.activate',
        'login',
        'register'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas connecté, rediriger vers login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Si ce n'est pas un caissier, on laisse passer
        if (!$user->isCaissier()) {
            return $next($request);
        }
        
        // Si c'est un caissier, vérifier si la route est autorisée
        $routeName = $request->route()->getName();
        
        // Si la route n'a pas de nom, vérifier le chemin
        if (!$routeName) {
            $path = $request->path();
            if ($path === '/' || $path === '') {
                return $next($request);
            }
        }
        
        if (!in_array($routeName, $this->allowedRoutes)) {
            abort(403, 'Accès non autorisé. Les caissiers n\'ont pas accès à cette page.');
        }
        
        return $next($request);
    }
} 