<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $permissions = array_map('trim', explode(',', $permission));

        if (empty($permissions)) {
            return $next($request);
        }

        foreach ($permissions as $perm) {
            if ($request->user()->can($perm)) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard')
            ->with('error', "Accès refusé : permission insuffisante.");
    }
}
