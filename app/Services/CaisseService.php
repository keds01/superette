<?php

namespace App\Services;

use App\Models\Vente;
use App\Models\DetailVente;
use App\Services\ProduitService;
use App\Models\Categorie;
use App\Models\Remise;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CaisseService
{
    protected $promotionService;
    protected $stockService;
    
    public function __construct(PromotionService $promotionService, StockService $stockService)
    {
        $this->promotionService = $promotionService;
        $this->stockService = $stockService;
    }
    
    /**
     * Crée une nouvelle vente avec ses détails
     * 
     * @param array $items Items du panier
     * @param array $paiement Informations de paiement
     * @param array $client Informations client (optionnel)
     * @param Remise|null $remise Remise appliquée (optionnel)
     * @param User $caissier Utilisateur caissier
     * @return Vente La vente créée
     */
    public function creerVente(array $items, array $paiement, ?array $client, ?Remise $remise, User $caissier)
    {
        DB::beginTransaction();
        
        try {
            // Création de la vente
            $vente = new Vente();
            $vente->reference = $this->genererReferenceVente();
            $vente->date_vente = now();
            $vente->user_id = $caissier->id;
            
            // Initialisation des montants
            $montantHT = 0;
            $montantTTC = 0;
            $montantTVA = 0;
            
            // Remplissage des informations client si fournies
            if ($client && !empty($client['nom'])) {
                $vente->client_nom = $client['nom'];
                $vente->client_telephone = $client['telephone'] ?? null;
                $vente->client_email = $client['email'] ?? null;
            }
            
            // Application de la remise si fournie
            if ($remise) {
                $vente->remise_id = $remise->id;
            }
            
            $vente->save();
            
            // Création des détails de vente pour chaque item
            foreach ($items as $item) {
                $produit = Product::findOrFail($item['produit_id']);
                
                // Application des promotions
                $infoPromotion = $this->promotionService->calculatePromotionPrice($produit);
                $prixUnitaire = $infoPromotion['prix_final'];
                $montantPromotion = $infoPromotion['reduction'] * $item['quantite'];
                
                // Conversion des quantités si exprimées en cartons+unités
                $quantiteTotal = isset($item['cartons']) && isset($item['unites'])
                    ? $this->stockService->convertToUnits($item['cartons'], $item['unites'], $produit->quantite_par_conditionnement)
                    : $item['quantite'];
                
                // Création du détail de vente
                $detail = new DetailVente();
                $detail->vente_id = $vente->id;
                $detail->produit_id = $produit->id; // Corrigé product_id en produit_id
                $detail->quantite = $quantiteTotal;
                $detail->prix_unitaire_ht = $produit->prix_vente_ht;
                $detail->prix_unitaire_ttc = $produit->prix_vente_ttc;
                $detail->prix_vente_unitaire = $prixUnitaire; // Prix après promotion
                $detail->prix_achat_unitaire = $produit->prix_achat_ht; // Ajout du prix d'achat unitaire
                $detail->taux_tva = $produit->tva;
                $detail->montant_ht = $produit->prix_vente_ht * $quantiteTotal;
                $detail->montant_ttc = $prixUnitaire * $quantiteTotal;
                $detail->montant_tva = $detail->montant_ttc - $detail->montant_ht;
                
                // Informations sur la promotion si applicable
                if ($infoPromotion['promotion']) {
                    $detail->promotion_id = $infoPromotion['promotion']->id;
                    $detail->montant_promotion = $montantPromotion;
                }
                
                $detail->save();
                
                // Mise à jour des montants totaux
                $montantHT += $detail->montant_ht;
                $montantTTC += $detail->montant_ttc;
                $montantTVA += $detail->montant_tva;
                
                // Mise à jour du stock
                $this->stockService->removeStock(
                    $produit,
                    0, // Cartons = 0 car on utilise directement la quantité totale
                    $quantiteTotal,
                    "Vente #{$vente->reference}"
                );
            }
            
            // Mise à jour des montants de la vente
            $vente->montant_ht = $montantHT;
            $vente->montant_ttc = $montantTTC;
            $vente->montant_tva = $montantTVA;
            
            // Application de la remise au niveau de la vente
            if ($remise) {
                $vente->montant_remise = $remise->montant_remise;
                $vente->montant_final = $remise->montant_final;
            } else {
                $vente->montant_final = $montantTTC;
            }
            
            $vente->save();
            
            // Création du paiement
            $paiementModel = new Paiement();
            $paiementModel->vente_id = $vente->id;
            $paiementModel->methode = $paiement['methode'];
            $paiementModel->montant = $vente->montant_final;
            $paiementModel->date_paiement = now();
            $paiementModel->reference_transaction = $paiement['reference'] ?? null;
            
            // Gestion du cas espèces avec monnaie rendue
            if ($paiement['methode'] === 'especes' && isset($paiement['montant_recu'])) {
                $paiementModel->montant_recu = $paiement['montant_recu'];
                $paiementModel->monnaie_rendue = $paiement['montant_recu'] - $vente->montant_final;
                
                // Option "Arrondi" pour dons
                if (isset($paiement['arrondi']) && $paiement['arrondi']) {
                    $paiementModel->montant_arrondi = ceil($vente->montant_final / 100) * 100;
                    $paiementModel->don = $paiementModel->montant_arrondi - $vente->montant_final;
                }
            }
            
            // Information Mobile Money
            if ($paiement['methode'] === 'mobile_money') {
                $paiementModel->operateur = $paiement['operateur'] ?? null;
                $paiementModel->numero_transaction = $paiement['numero_transaction'] ?? null;
            }
            
            $paiementModel->statut = 'validé';
            $paiementModel->save();
            
            DB::commit();
            
            return $vente;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Annule une vente et ses détails, et restitue les stocks
     * 
     * @param Vente $vente Vente à annuler
     * @param string $motif Motif de l'annulation
     * @param User $utilisateur Utilisateur effectuant l'annulation
     * @return bool Succès de l'opération
     */
    public function annulerVente(Vente $vente, string $motif, User $utilisateur)
    {
        // Vérification des droits
        if (!$this->peutAnnulerVente($vente, $utilisateur)) {
            throw new \Exception("Vous n'avez pas les droits pour annuler cette vente.");
        }
        
        DB::beginTransaction();
        
        try {
            // Annulation des paiements associés
            foreach ($vente->paiements as $paiement) {
                $paiement->statut = 'annulé';
                $paiement->date_annulation = now();
                $paiement->motif_annulation = $motif;
                $paiement->user_id_annulation = $utilisateur->id;
                $paiement->save();
            }
            
            // Restitution des stocks pour chaque produit vendu
            foreach ($vente->detailsVente as $detail) {
                $produit = $detail->product;
                
                // Ajout du stock
                $this->stockService->addStock(
                    $produit,
                    0, // Cartons = 0 car on utilise directement la quantité totale
                    $detail->quantite,
                    "Annulation vente #{$vente->reference}",
                    $detail->prix_unitaire_ht
                );
            }
            
            // Marquer la vente comme annulée
            $vente->statut = 'annulé';
            $vente->date_annulation = now();
            $vente->motif_annulation = $motif;
            $vente->user_id_annulation = $utilisateur->id;
            $vente->save();
            
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Vérifie si un utilisateur peut annuler une vente
     * 
     * @param Vente $vente Vente à annuler
     * @param User $utilisateur Utilisateur effectuant l'annulation
     * @return bool
     */
    public function peutAnnulerVente(Vente $vente, User $utilisateur)
    {
        // Vérification si la méthode hasRole existe
        $hasRoleMethod = method_exists($utilisateur, 'hasRole');
        
        // Un gérant peut toujours annuler
        if ($hasRoleMethod && $utilisateur->hasRole('Gérant')) {
            return true;
        }
        
        // Vérification alternative du rôle Gérant
        if (!$hasRoleMethod && ($utilisateur->role === 'Gérant' || $utilisateur->role_id === 1)) {
            return true;
        }
        
        // Un responsable peut annuler si le montant est < 10,000 FCFA
        if ($vente->montant_final < 10000) {
            if ($hasRoleMethod && $utilisateur->hasRole('Responsable')) {
                return true;
            }
            
            // Vérification alternative du rôle Responsable
            if (!$hasRoleMethod && ($utilisateur->role === 'Responsable' || $utilisateur->role_id === 2)) {
                return true;
            }
        }
        
        // Un caissier ne peut pas annuler
        return false;
    }
    
    /**
     * Génère une référence unique pour une vente
     * 
     * @return string
     */
    private function genererReferenceVente()
    {
        $prefix = 'V-' . date('Ymd');
        $lastVente = Vente::where('reference', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
            
        $counter = 1;
        
        if ($lastVente) {
            $parts = explode('-', $lastVente->reference);
            $counter = (int)end($parts) + 1;
        }
        
        return $prefix . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Génère un QR code pour paiement Mobile Money
     * 
     * @param float $montant Montant à payer
     * @param string $operateur Opérateur (MTN, MOOV, etc.)
     * @return array Informations du QR Code
     */
    public function genererQRCodeMobileMoney($montant, $operateur)
    {
        // Génération d'un identifiant unique pour la transaction
        $transactionId = strtoupper(Str::random(8));
        
        // Dans une implémentation réelle, on intégrerait une API comme MTN MoMo
        // Pour l'instant, on simule les données de retour
        
        return [
            'transaction_id' => $transactionId,
            'montant' => $montant,
            'operateur' => $operateur,
            'qr_code_data' => "MOMO:{$operateur}:{$transactionId}:{$montant}",
            'url_validation' => "/api/caisse/valider-mobile-money/{$transactionId}",
            'expire_at' => now()->addMinutes(10)->toIso8601String()
        ];
    }
    
    /**
     * Prépare les données pour l'impression d'un ticket de caisse
     * 
     * @param Vente $vente Vente concernée
     * @return array Données formatées pour l'impression
     */
    public function preparerTicketCaisse(Vente $vente)
    {
        // Récupération des paramètres du magasin
        $nomMagasin = config('app.nom_magasin', 'Superette');
        $adresseMagasin = config('app.adresse_magasin', 'Lomé, Togo');
        $telephoneMagasin = config('app.telephone_magasin', '');
        $mentionsLegales = config('app.mentions_legales_ticket', 'Merci pour votre achat');
        
        // Formatage des lignes du ticket
        $lignes = $vente->detailsVente->map(function ($detail) {
            $nomProduit = $detail->product->nom;
            $prix = number_format($detail->prix_vente_unitaire, 0, ',', ' ');
            $quantite = $detail->quantite;
            $total = number_format($detail->montant_ttc, 0, ',', ' ');
            
            $ligne = [
                'produit' => $nomProduit,
                'quantite' => $quantite,
                'prix_unitaire' => $prix,
                'total' => $total
            ];
            
            // Ajout des informations de promotion si applicable
            if ($detail->promotion_id) {
                $ligne['promotion'] = [
                    'libelle' => $detail->promotion->libelle,
                    'montant' => number_format($detail->montant_promotion, 0, ',', ' ')
                ];
            }
            
            return $ligne;
        });
        
        // Informations de paiement
        $paiement = $vente->paiements->first();
        $infoPaiement = [
            'methode' => $paiement ? $paiement->methode : 'N/A',
            'montant_recu' => $paiement && $paiement->montant_recu ? number_format($paiement->montant_recu, 0, ',', ' ') : null,
            'monnaie_rendue' => $paiement && $paiement->monnaie_rendue ? number_format($paiement->monnaie_rendue, 0, ',', ' ') : null,
            'reference' => $paiement && $paiement->reference_transaction ? $paiement->reference_transaction : null,
            'don' => $paiement && $paiement->don ? number_format($paiement->don, 0, ',', ' ') : null
        ];
        
        // Structure complète du ticket
        $ticket = [
            'entete' => [
                'nom_magasin' => $nomMagasin,
                'adresse' => $adresseMagasin,
                'telephone' => $telephoneMagasin,
                'date' => $vente->date_vente->format('d/m/Y H:i'),
                'reference' => $vente->reference,
                'caissier' => $vente->user->name
            ],
            'lignes' => $lignes,
            'totaux' => [
                'ht' => number_format($vente->montant_ht, 0, ',', ' '),
                'tva' => number_format($vente->montant_tva, 0, ',', ' '),
                'ttc' => number_format($vente->montant_ttc, 0, ',', ' '),
                'remise' => $vente->montant_remise ? number_format($vente->montant_remise, 0, ',', ' ') : null,
                'final' => number_format($vente->montant_final, 0, ',', ' ')
            ],
            'paiement' => $infoPaiement,
            'client' => $vente->client_nom ? [
                'nom' => $vente->client_nom,
                'telephone' => $vente->client_telephone,
                'email' => $vente->client_email
            ] : null,
            'pied' => [
                'mentions_legales' => $mentionsLegales,
                'duplicata' => $vente->statut === 'duplicata',
                'annule' => $vente->statut === 'annulé'
            ]
        ];
        
        return $ticket;
    }
}
