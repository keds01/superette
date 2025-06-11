<?php

namespace App\Services;

use App\Services\ProduitService;
use App\Models\Promotion;
use App\Models\Remise;
use App\Models\Categorie;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PromotionService
{
    /**
     * Obtient toutes les promotions actives à la date actuelle
     * 
     * @return Collection Collection de promotions actives
     */
    public function getActivePromotions()
    {
        return Promotion::where('actif', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->get();
    }
    
    /**
     * Obtient les promotions actives pour un produit spécifique
     * 
     * @param Product $product Produit concerné
     * @return Collection Collection de promotions actives
     */
    public function getPromotionsForProduct(Product $product)
    {
        $promotions = collect();
        
        // Promotions directes sur le produit
        $directPromotions = Promotion::where('produit_id', $product->id)
            ->where('actif', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->get();
        
        $promotions = $promotions->merge($directPromotions);
        
        // Promotions sur la catégorie
        $categoryPromotions = Promotion::where('categorie_id', $product->categorie_id)
            ->whereNull('produit_id')
            ->where('actif', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->get();
        
        $promotions = $promotions->merge($categoryPromotions);
        
        // Promotions globales (sans produit ni catégorie spécifique)
        $globalPromotions = Promotion::whereNull('produit_id')
            ->whereNull('categorie_id')
            ->where('actif', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->get();
        
        $promotions = $promotions->merge($globalPromotions);
        
        return $promotions;
    }
    
    /**
     * Calcule le prix après promotion pour un produit
     * 
     * @param Product $product Produit concerné
     * @return array Informations sur le prix après promotion
     */
    public function calculatePromotionPrice(Product $product)
    {
        $prixOriginal = $product->prix_vente_ttc;
        $promotions = $this->getPromotionsForProduct($product);
        
        if ($promotions->isEmpty()) {
            return [
                'prix_final' => $prixOriginal,
                'reduction' => 0,
                'promotion' => null
            ];
        }
        
        // Appliquer la promotion la plus avantageuse
        $meilleurPrix = $prixOriginal;
        $meilleurPromotion = null;
        $meilleurReduction = 0;
        
        foreach ($promotions as $promotion) {
            $prixPromo = $this->applyPromotion($product, $promotion);
            
            if ($prixPromo < $meilleurPrix) {
                $meilleurPrix = $prixPromo;
                $meilleurPromotion = $promotion;
                $meilleurReduction = $prixOriginal - $prixPromo;
            }
        }
        
        return [
            'prix_final' => $meilleurPrix,
            'reduction' => $meilleurReduction,
            'promotion' => $meilleurPromotion
        ];
    }
    
    /**
     * Applique une promotion spécifique à un produit
     * 
     * @param Product $product Produit concerné
     * @param Promotion $promotion Promotion à appliquer
     * @return float Prix après application de la promotion
     */
    public function applyPromotion(Product $product, Promotion $promotion)
    {
        $prixOriginal = $product->prix_vente_ttc;
        
        switch ($promotion->type) {
            case 'pourcentage':
                // Réduction en pourcentage
                return $prixOriginal * (1 - ($promotion->valeur / 100));
                
            case 'montant':
            case 'montant_fixe':
                // Réduction d'un montant fixe
                return max(0, $prixOriginal - $promotion->valeur);
                
            case 'prix_fixe':
                // Prix fixe (ne pas dépasser le prix original)
                return min($prixOriginal, $promotion->valeur);
                
            case 'bundle':
                // Promotion de type "N pour X" (ex: 3 pour 2)
                // Format de valeur: "3,2" signifie "acheter 3, payer 2"
                $parts = explode(',', $promotion->valeur);
                if (count($parts) === 2) {
                    $acheter = (int)$parts[0];
                    $payer = (int)$parts[1];
                    
                    if ($acheter > $payer) {
                        // Prix unitaire équivalent: (payer / acheter) * prix original
                        return ($payer / $acheter) * $prixOriginal;
                    }
                }
                break;
        }
        
        // Par défaut, retourner le prix original
        return $prixOriginal;
    }
    
    /**
     * Calcule le prix après remise pour un panier
     * 
     * @param float $totalPanier Montant total du panier
     * @param string $typeRemise Type de remise (pourcentage, montant_fixe)
     * @param float $valeurRemise Valeur de la remise
     * @param string $motif Motif de la remise
     * @param int|null $userIdResponsable ID de l'utilisateur responsable
     * @return array Informations sur le prix après remise
     */
    public function calculateRemise($totalPanier, $typeRemise, $valeurRemise, $motif, $userIdResponsable = null)
    {
        $reduction = 0;
        
        if ($typeRemise === 'pourcentage') {
            // Limiter à 10% sans validation du gérant
            $valeurMax = 10;
            if ($valeurRemise > $valeurMax && !$this->isGerant($userIdResponsable)) {
                $valeurRemise = $valeurMax;
            }
            
            $reduction = $totalPanier * ($valeurRemise / 100);
        } elseif ($typeRemise === 'montant_fixe') {
            $reduction = min($totalPanier, $valeurRemise);
        }
        
        $prixFinal = $totalPanier - $reduction;
        
        // Création de l'enregistrement de remise
        $remise = Remise::create([
            'type' => $typeRemise,
            'valeur' => $valeurRemise,
            'montant_remise' => $reduction,
            'montant_initial' => $totalPanier,
            'montant_final' => $prixFinal,
            'motif' => $motif,
            'date_remise' => now(),
            'user_id' => $userIdResponsable
        ]);
        
        return [
            'prix_final' => $prixFinal,
            'reduction' => $reduction,
            'remise' => $remise
        ];
    }
    
    /**
     * Vérifie si un utilisateur est gérant
     * 
     * @param int|null $userId ID de l'utilisateur
     * @return bool
     */
    private function isGerant($userId)
    {
        if (!$userId) {
            return false;
        }
        
        $user = User::find($userId);
        
        if (!$user) {
            return false;
        }
        
        // Vérification du rôle soit par la relation directe, soit par un attribut
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('Gérant');
        }
        
        // Alternative si hasRole n'existe pas
        return $user->role_id === 1 || $user->role === 'Gérant' || $user->is_admin === true;
    }
}
