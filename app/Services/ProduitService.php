<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ProduitService
{
    /**
     * Crée un nouveau produit avec gestion optimisée des calculs et de l'image
     * 
     * @param array $data
     * @return Produit
     */
    public function createProduct(array $data)
    {
        try {
            DB::beginTransaction();
            
            // Utilisation des valeurs pré-calculées si présentes, sinon calcul
            if (isset($data['prix_vente_ht']) && isset($data['prix_vente_ttc'])) {
                $prix_vente_ht = (float) $data['prix_vente_ht'];
                $prix_vente_ttc = (float) $data['prix_vente_ttc'];
            } else {
                // Calcul des prix via la méthode dédiée
                $calculated = $this->calculatePrices(
                    (float) $data['prix_achat_ht'], 
                    (float) $data['marge'], 
                    (float) $data['tva']
                );
                $prix_vente_ht = $calculated['prix_vente_ht'];
                $prix_vente_ttc = $calculated['prix_vente_ttc'];
            }

            // Création du produit avec données de base
            $produitData = [
                'nom' => $data['nom'],
                'reference' => $data['reference'],
                'code_barres' => $data['code_barres'] ?? null,
                'categorie_id' => (int) $data['categorie_id'],
                'description' => $data['description'] ?? null,
                'unite_vente_id' => (int) $data['unite_vente_id'],
                'conditionnement_fournisseur' => $data['conditionnement_fournisseur'],
                'quantite_par_conditionnement' => (float) ($data['quantite_par_conditionnement'] ?? 1),
                'stock' => (float) ($data['stock_initial'] ?? 0),
                'seuil_alerte' => (float) $data['seuil_alerte'],
                'emplacement_rayon' => $data['emplacement_rayon'],
                'emplacement_etagere' => $data['emplacement_etagere'],
                'date_peremption' => $data['date_peremption'] ?? null,
                'prix_achat_ht' => (float) $data['prix_achat_ht'],
                'prix_vente_ht' => round($prix_vente_ht, 2),
                'prix_vente_ttc' => round($prix_vente_ttc, 2),
                'marge' => (float) $data['marge'],
                'tva' => (float) $data['tva'],
                'actif' => true
            ];
            
            $produit = Produit::create($produitData);
            Log::info('Produit créé avec succès', ['id' => $produit->id, 'nom' => $produit->nom]);

            // Traitement de l'image avec gestion d'erreurs
            if (isset($data['image']) && !empty($data['image'])) {
                $this->processProductImage($produit, $data['image']);
            }

            // Création du mouvement de stock initial (si stock > 0)
            if ((float)($data['stock_initial'] ?? 0) > 0) {
                $this->createStockMovement($produit, [
                    'type' => 'entree',
                    'quantite' => (float) $data['stock_initial'],
                    'prix_unitaire' => (float) $data['prix_achat_ht'],
                    'motif' => 'Stock initial',
                    'date_peremption' => $data['date_peremption'] ?? null
                ]);
            }
            
            DB::commit();
            return $produit;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du produit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() 
            ]);
            throw $e;
        }
    }

    /**
     * Mise à jour d'un produit avec gestion optimisée
     *
     * @param Produit $produit
     * @param array $data
     * @return Produit
     */
    public function updateProduct(Produit $produit, array $data)
    {
        try {
            DB::beginTransaction();
            
            // Recalcul des prix si nécessaire
            if (isset($data['prix_achat_ht']) || isset($data['marge']) || isset($data['tva'])) {
                $prix_achat_ht = $data['prix_achat_ht'] ?? $produit->prix_achat_ht;
                $marge = $data['marge'] ?? $produit->marge;
                $tva = $data['tva'] ?? $produit->tva;
                
                // Utiliser les valeurs calculées si présentes
                if (!isset($data['prix_vente_ht']) || !isset($data['prix_vente_ttc'])) {
                    $calculated = $this->calculatePrices((float) $prix_achat_ht, (float) $marge, (float) $tva);
                    $data['prix_vente_ht'] = $calculated['prix_vente_ht'];
                    $data['prix_vente_ttc'] = $calculated['prix_vente_ttc'];
                }
            }

            // Mise à jour du produit
            $produit->update($data);

            // Gestion optimisée de l'image
            if (isset($data['image']) && !empty($data['image'])) {
                $this->processProductImage($produit, $data['image']);
            }

            // Vérification des alertes
            if ($produit->stock <= $produit->seuil_alerte) {
                $this->createStockAlert($produit);
            }

            DB::commit();
            return $produit;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du produit', [
                'id' => $produit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function createStockMovement(Produit $produit, array $data)
    {
        return MouvementStock::create([
            'produit_id' => $produit->id,
            'type' => $data['type'],
            'quantite' => $data['quantite'],
            'prix_unitaire' => $data['prix_unitaire'] ?? $produit->prix_achat_ht,
            'date_mouvement' => $data['date_mouvement'] ?? now(),
            'motif' => $data['motif'],
            'date_peremption' => $data['date_peremption'] ?? null,
            'user_id' => auth()->id()
        ]);
    }

    public function createStockAlert(Produit $produit)
    {
        return \App\Models\Alerte::create([
            'type' => 'stock_bas',
            'produit_id' => $produit->id,
            'message' => "Stock bas pour le produit {$produit->nom}",
            'seuil' => $produit->seuil_alerte
        ]);
    }

    public function calculatePrices(float $prix_achat_ht, float $marge, float $tva): array
    {
        $marge = $marge / 100;
        $tva = $tva / 100;
        
        $prix_vente_ht = $prix_achat_ht * (1 + $marge);
        $prix_vente_ttc = $prix_vente_ht * (1 + $tva);

        return [
            'prix_vente_ht' => $prix_vente_ht,
            'prix_vente_ttc' => $prix_vente_ttc
        ];
    }
    
    /**
     * Calcule les statistiques pour un produit donné.
     *
     * @param Produit $produit
     * @return array
     */
    public function getStatistiquesProduit(Produit $produit): array
    {
        // Nous utilisons 'quantite' comme référence pour les calculs
        $totalVentes = MouvementStock::where('produit_id', $produit->id)
        ->where('type', 'sortie')
        ->get()
        ->sum(function($mvt) {
            return ($mvt->quantite_apres_unite - $mvt->quantite_avant_unite);
        });

        $totalEntrees = MouvementStock::where('produit_id', $produit->id)
        ->where('type', 'entree')
        ->get()
        ->sum(function($mvt) {
            return ($mvt->quantite_apres_unite - $mvt->quantite_avant_unite);
        });

        // La valeur du stock est calculée à partir du stock actuel et du prix d'achat HT.
        $valeurStock = $produit->stock * $produit->prix_achat_ht;

        $dernierMouvement = MouvementStock::where('produit_id', $produit->id)
        ->orderBy('created_at', 'desc')
        ->first();

        return [
            'total_ventes' => $totalVentes ?? 0,
            'total_entrees' => $totalEntrees ?? 0,
            'valeur_stock' => $valeurStock ?? 0,
            'dernier_mouvement' => $dernierMouvement,
        ];
    }
    
    /**
     * Alias pour getStatistiquesProduit, pour maintenir la compatibilité avec le code existant.
     *
     * @param Produit $produit
     * @return array
     */
    public function getProductStats(Produit $produit): array
    {
        return $this->getStatistiquesProduit($produit);
    }
    
    /**
     * Traite et sauvegarde l'image du produit avec redimensionnement
     *
     * @param Produit $produit
     * @param mixed $imageFile
     * @return void
     */
    private function processProductImage(Produit $produit, $imageFile)
    {
        try {
            // Si le produit a déjà une image, on la supprime
            if ($produit->image) {
                Storage::disk('public')->delete($produit->image);
            }
            
            // Génération d'un nom unique pour l'image
            $imageName = 'product_' . $produit->id . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
            
            // Utiliser Intervention Image si disponible pour optimiser l'image
            if (class_exists('\Intervention\Image\Facades\Image')) {
                $img = \Intervention\Image\Facades\Image::make($imageFile)
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode(null, 75); // Compression de l'image
                    
                Storage::disk('public')->put('products/' . $imageName, $img);
            } else {
                // Méthode standard si Intervention n'est pas disponible
                Storage::disk('public')->putFileAs('products', $imageFile, $imageName);
            }
            
            // Mettre à jour le chemin de l'image dans le produit
            $produit->image = 'products/' . $imageName;
            $produit->save();
            
            Log::info('Image produit enregistrée avec succès', ['id' => $produit->id, 'image' => $imageName]);
        } catch (\Exception $e) {
            Log::error('Erreur traitement image produit', [
                'id' => $produit->id, 
                'error' => $e->getMessage()
            ]);
            // On ne lève pas l'exception pour ne pas empêcher la création du produit
        }
    }
}