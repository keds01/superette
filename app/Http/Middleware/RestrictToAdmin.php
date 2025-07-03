<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RestrictToAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        $user = $request->user();
        
        // Vérifier si l'utilisateur est admin ou super_admin en utilisant les méthodes du modèle
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $next($request);
        }
        
        // Journaliser la tentative d'accès non autorisée
        Log::warning('Tentative d\'accès à une zone réservée aux administrateurs', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);
        
        return response()
            ->view('errors.403', [
                'message' => "Cette section est réservée aux administrateurs.",
                'permission' => null
            ], 403);
    }
}
