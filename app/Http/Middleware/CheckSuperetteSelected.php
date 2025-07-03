<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperetteSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check if user is on the superette selection page or routes that don't require selection
        if ($request->routeIs('superettes.select') || $request->routeIs('superettes.activate') || 
            $request->routeIs('login') || $request->routeIs('logout') || $request->routeIs('register')) {
            return $next($request);
        }
        
        // Check if a superette is selected for any authenticated user
        if (!session('active_superette_id') && auth()->check()) {
            // Si l'utilisateur a une superette par défaut, l'activer automatiquement
            if (auth()->user()->superette_id) {
                session(['active_superette_id' => auth()->user()->superette_id]);
                return $next($request);
            }
            
            // Sinon, rediriger vers la page de sélection
            return redirect()->route('superettes.select')
                ->with('info', 'Veuillez sélectionner une superette avant de continuer.');
        }
        
        return $next($request);
    }
} 