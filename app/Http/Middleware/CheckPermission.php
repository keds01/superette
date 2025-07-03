<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    /**
     * Vérifie si l'utilisateur possède au moins une des permissions spécifiées.
     * Plusieurs permissions peuvent être passées séparées par une virgule.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        // Si l'utilisateur n'est pas connecté, redirection vers la page de connexion
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

        // Préparation des permissions à vérifier (séparées par des virgules)
        $permissions = array_map('trim', explode(',', $permission));

        // Si aucune permission n'est requise, on continue
        if (empty($permissions)) {
            return $next($request);
        }

        // Les super-admin ont accès à tout
        if ($request->user()->hasRole('super-admin') || $request->user()->hasRole('super_admin')) {
            return $next($request);
        }

        // Vérification : l'utilisateur possède-t-il au moins une des permissions ?
        foreach ($permissions as $perm) {
            if ($request->user()->can($perm)) { // utilise User::can qui journalise aussi les refus
                return $next($request);
            }
        }

        // Journalisation de la tentative d'accès non autorisée
        Log::warning('Tentative d\'accès non autorisé - Permission requise manquante', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
            'permissions_utilisateur' => $request->user()->roles->flatMap->permissions->pluck('name')->unique()->toArray(),
            'permissions_requises' => $permissions,
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);

        // Si l'utilisateur n'a pas la permission requise, afficher 403 personnalisé
        return response()
            ->view('errors.403', [
                'message' => "Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.",
                'permission' => implode(', ', $permissions)
            ], 403);
    }
}
