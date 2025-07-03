<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\MouvementStock;
use App\Services\AlertService;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Service d'alertes
     *
     * @var AlertService
     */
    protected $alertService;
    
    /**
     * Constructeur
     *
     * @param AlertService $alertService
     */
    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }
    
    /**
     * Convertit une quantité exprimée en cartons+unités vers le nombre total d'unités
     * 
     * @param int $cartons Nombre de cartons
     * @param float $unites Nombre d'unités individuelles
     * @param float $quantiteParConditionnement Nombre d'unités par carton
     * @return float Total des unités
     */
    public function convertToUnits($cartons, $unites, $quantiteParConditionnement)
    {
        return ($cartons * $quantiteParConditionnement) + $unites;
    }
    
    /**
     * Convertit un nombre total d'unités en une représentation cartons+unités
     * 
     * @param float $totalUnites Total des unités
     * @param float $quantiteParConditionnement Nombre d'unités par carton
     * @return array ['cartons' => int, 'unites' => float]
     */
    public function convertFromUnits($totalUnites, $quantiteParConditionnement)
    {
        // Vérification pour éviter une division par zéro
        if ($quantiteParConditionnement <= 0) {
            return [
                'cartons' => 0,
                'unites' => $totalUnites
            ];
        }
        
        $cartons = floor($totalUnites / $quantiteParConditionnement);
        $unites = $totalUnites - ($cartons * $quantiteParConditionnement);
        
        return [
            'cartons' => $cartons,
            'unites' => $unites
        ];
    }
    
    /**
     * Ajoute du stock à un produit (entrée de stock)
     * 
     * @param Produit $produit Produit concerné
     * @param int $cartons Nombre de cartons
     * @param float $unites Nombre d'unités individuelles
     * @param string $motif Motif du mouvement de stock
     * @param float $prixUnitaire Prix unitaire d'achat
     * @param string|null $datePeremption Date de péremption (format Y-m-d)
     * @return MouvementStock Le mouvement de stock créé
     */
    public function addStock(Produit $produit, $cartons, $unites, $motif, $prixUnitaire, $datePeremption = null)
    {
        $totalUnites = $this->convertToUnits($cartons, $unites, $produit->quantite_par_conditionnement);
        
        DB::beginTransaction();
        try {
            // Mise à jour du stock du produit
            $produit->stock += $totalUnites;
            $produit->save();
            
            // Création du mouvement de stock
            $stockMovement = MouvementStock::create([
                'produit_id' => $produit->id,
                'type' => 'entree',
                'quantite' => $totalUnites,
                'prix_unitaire' => $prixUnitaire,
                'created_at' => now(),
                'motif' => $motif,
                'date_peremption' => $datePeremption,
                'superette_id' => $produit->superette_id ?? session('active_superette_id') ?? (auth()->check() ? auth()->user()->superette_id : 1)
            ]);
            
            // Vérification des alertes de stock pour ce produit
            $this->alertService->checkProductStockAlert($produit);
            
            DB::commit();
            return $stockMovement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Retire du stock à un produit (sortie de stock)
     * 
     * @param Produit $produit Produit concerné
     * @param int $cartons Nombre de cartons
     * @param float $unites Nombre d'unités individuelles
     * @param string $motif Motif du mouvement de stock
     * @return MouvementStock|null Le mouvement de stock créé, ou null si stock insuffisant
     */
    public function removeStock(Produit $produit, $cartons, $unites, $motif)
    {
        $totalUnites = $this->convertToUnits($cartons, $unites, $produit->quantite_par_conditionnement);
        
        // Vérification que le stock est suffisant
        if ($produit->stock < $totalUnites) {
            return null;
        }
        
        DB::beginTransaction();
        try {
            // Mise à jour du stock du produit
            $produit->stock -= $totalUnites;
            $produit->save();
            
            // Création du mouvement de stock
            $stockMovement = MouvementStock::create([
                'produit_id' => $produit->id,
                'type' => 'sortie',
                'quantite' => $totalUnites,
                'prix_unitaire' => $produit->prix_achat_ht,
                'created_at' => now(),
                'motif' => $motif,
                'superette_id' => $produit->superette_id ?? session('active_superette_id') ?? (auth()->check() ? auth()->user()->superette_id : 1)
            ]);
            
            // Vérification des alertes de stock pour ce produit
            $this->alertService->checkProductStockAlert($produit);
            
            DB::commit();
            return $stockMovement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Ajuste le stock d'un produit (inventaire)
     * 
     * @param Produit $produit Produit concerné
     * @param int $cartons Nombre de cartons constatés
     * @param float $unites Nombre d'unités individuelles constatées
     * @param string $motif Motif de l'ajustement
     * @return MouvementStock Le mouvement de stock créé
     */
    public function adjustStock(Produit $produit, $cartons, $unites, $motif)
    {
        $newTotalUnites = $this->convertToUnits($cartons, $unites, $produit->quantite_par_conditionnement);
        $difference = $newTotalUnites - $produit->stock;
        
        DB::beginTransaction();
        try {
            // Mise à jour du stock du produit
            $produit->stock = $newTotalUnites;
            $produit->save();
            
            // Création du mouvement de stock
            $stockMovement = MouvementStock::create([
                'produit_id' => $produit->id,
                'type' => $difference >= 0 ? 'ajustement_positif' : 'ajustement_negatif',
                'quantite' => abs($difference),
                'prix_unitaire' => $produit->prix_achat_ht,
                'created_at' => now(),
                'motif' => $motif,
                'superette_id' => $produit->superette_id ?? session('active_superette_id') ?? (auth()->check() ? auth()->user()->superette_id : 1)
            ]);
            
            // Vérification des alertes de stock pour ce produit
            $this->alertService->checkProductStockAlert($produit);
            
            DB::commit();
            return $stockMovement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
