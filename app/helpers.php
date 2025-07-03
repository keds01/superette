<?php

use App\Models\Superette;
use Illuminate\Support\Facades\DB;

/**
 * Determine if a given path is active
 * 
 * @param string|array $paths
 * @param bool $exact
 * @return bool
 */
function isLinkActive($paths, $exact = false): bool
{
    if (!is_array($paths)) {
        $paths = [$paths];
    }

    foreach ($paths as $path) {
        // Si le chemin contient un astérisque, utiliser un match de motif
        if (Str::contains($path, '*')) {
            if (request()->routeIs($path)) {
                return true;
            }
        } else {
            $routeName = request()->route()->getName();
            if ($exact) {
                if ($routeName === $path) {
                    return true;
                }
            } else {
                if (Str::startsWith($routeName, $path)) {
                    return true;
                }
            }
        }
    }
    
    return false;
}

/**
 * Retourne le statut de vente formaté pour l'affichage
 * 
 * @param string $status
 * @return array
 */
function getVenteStatusBadgeClass($status): array
{
    return match ($status) {
        'completed' => ['bg-green-100', 'text-green-800', 'Complétée'],
        'pending' => ['bg-yellow-100', 'text-yellow-800', 'En attente'],
        'cancelled' => ['bg-red-100', 'text-red-800', 'Annulée'],
        'processing' => ['bg-blue-100', 'text-blue-800', 'En traitement'],
        default => ['bg-gray-100', 'text-gray-800', 'Inconnu'],
    };
}

/**
 * Retourne l'ID de la superette active
 * 
 * @param mixed $default
 * @return int|null
 */
function activeSuperetteId($default = null)
{
    return session('active_superette_id', $default);
}

/**
 * Retourne l'instance de la superette active
 *
 * @return \App\Models\Superette|null
 */
function activeSuperette()
{
    $superetteId = session('active_superette_id');
    
    if (!$superetteId) {
        return null;
    }
    
    return Superette::find($superetteId);
}
