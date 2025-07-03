<?php

namespace App\Observers;

use App\Models\Produit;
use App\Models\Alerte;
use Illuminate\Support\Facades\Log;

class ProduitObserver
{
    /**
     * Surveille les mises à jour du produit
     * Vérifie si le stock est passé sous le seuil d'alerte et crée une alerte si nécessaire
     */
    public function updated(Produit $produit)
    {
        // Si le stock a été modifié et est inférieur ou égal au seuil d'alerte
        if ($produit->isDirty('stock') && $produit->stock <= $produit->seuil_alerte) {
            Log::info("Stock bas détecté pour {$produit->nom}: {$produit->stock} (seuil: {$produit->seuil_alerte})");
            
            // Vérifie si une alerte active existe déjà pour ce produit
            $alerteExistante = Alerte::where('produit_id', $produit->id)
                ->where('type', 'seuil_minimum')
                ->where('actif', true)
                ->first();
            
            if (!$alerteExistante) {
                // Récupérer la superette_id du produit
                $superetteId = $produit->superette_id;
                
                // Si le produit n'a pas de superette_id, utiliser celle de la session
                if (!$superetteId) {
                    $superetteId = session('active_superette_id');
                }
                
                // Si toujours pas de superette_id, utiliser celle de l'utilisateur connecté
                if (!$superetteId && auth()->check() && auth()->user()->superette_id) {
                    $superetteId = auth()->user()->superette_id;
                }
                
                // Si toujours pas de superette_id, utiliser la superette par défaut (ID 1)
                if (!$superetteId) {
                    $superetteId = 1; // Superette par défaut
                }
                
                // Crée une nouvelle alerte
                Alerte::create([
                    'produit_id' => $produit->id,
                    'type' => 'seuil_minimum',
                    'seuil' => $produit->seuil_alerte,
                    'message' => "Le stock de {$produit->nom} est inférieur au seuil minimum ({$produit->seuil_alerte})",
                    'estDeclenchee' => true,
                    'actif' => true,
                    'superette_id' => $superetteId
                ]);
                
                Log::info("Alerte créée pour le produit {$produit->nom}");
            } else if (!$alerteExistante->estDeclenchee) {
                // Si l'alerte existe mais n'est pas déclenchée, la déclencher
                $alerteExistante->update([
                    'estDeclenchee' => true,
                    'message' => "Le stock de {$produit->nom} est inférieur au seuil minimum ({$produit->seuil_alerte})"
                ]);
                
                Log::info("Alerte existante déclenchée pour le produit {$produit->nom}");
            }
        }
    }
}
