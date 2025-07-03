<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Alerte;
use App\Models\MouvementStock;
use App\Models\Vente;
use App\Models\Superette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Rediriger vers le tableau de bord admin si l'utilisateur est admin ou super-admin
        if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())) {
            return redirect()->route('dashboard.admin');
        }
        
        $activeSuperetteId = activeSuperetteId();
        $activeSuperette = activeSuperette();
        
        // Si pas de superette active et l'utilisateur est super-admin, rediriger vers la sélection
        if (!$activeSuperetteId && auth()->check()) {
            return redirect()->route('superettes.select')
                ->with('info', 'Veuillez sélectionner une superette pour accéder au tableau de bord.');
        }
        
        // Récupérer les catégories pour le filtre
        $categories = Categorie::orderBy('nom')->get();
        
        // Statistiques de base
        $stats = [
            'totalProduits' => Produit::where('superette_id', $activeSuperetteId)->count(),
            'produitsEnRupture' => Produit::where('superette_id', $activeSuperetteId)->where('stock', 0)->count(),
            'produitsSousSeuilAlerte' => Produit::where('superette_id', $activeSuperetteId)->whereRaw('stock <= seuil_alerte')->count(),
            'valeurStock' => Produit::where('superette_id', $activeSuperetteId)->sum(DB::raw('stock * prix_achat_ht')),
        ];
        
        // Ventes du jour
        $ventesAujourdhui = Vente::where('superette_id', $activeSuperetteId)
            ->whereDate('created_at', Carbon::today())
            ->count();
            
        $stats['ventesAujourdhui'] = $ventesAujourdhui;
        
        // Chiffre d'affaires du jour
        $caAujourdhui = Vente::where('superette_id', $activeSuperetteId)
            ->whereDate('created_at', Carbon::today())
            ->sum('montant_total');
            
        $stats['caAujourdhui'] = $caAujourdhui;
        
        // Mouvements de stock récents
        $mouvementsRecents = MouvementStock::with(['produit', 'utilisateur'])
            ->where('superette_id', $activeSuperetteId)
            ->latest()
            ->limit(5)
            ->get();
        
        // Produits en rupture ou presque
        $produitsEnAlerte = Produit::with('categorie')
            ->where('superette_id', $activeSuperetteId)
            ->whereRaw('stock <= seuil_alerte')
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();
            
        // Produits en alerte de stock (pour le tableau des produits en alerte)
        $lowStockProducts = Produit::with('uniteVente')
            ->where('superette_id', $activeSuperetteId)
            ->whereRaw('stock <= seuil_alerte')
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
            
        // Produits à péremption proche (pour le tableau des produits à péremption)
        $expiringProducts = collect(); // Par défaut, collection vide
        
        // Vérifier si la table mouvements_stock a une colonne date_peremption
        if (Schema::hasColumn('mouvements_stock', 'date_peremption')) {
            $expiringProducts = DB::table('mouvements_stock')
                ->join('produits', 'mouvements_stock.produit_id', '=', 'produits.id')
                ->where('mouvements_stock.superette_id', $activeSuperetteId)
                ->whereNotNull('mouvements_stock.date_peremption')
                ->where('mouvements_stock.date_peremption', '>', now())
                ->where('mouvements_stock.date_peremption', '<=', now()->addDays(30))
                ->select(
                    'produits.id',
                    'produits.nom',
                    'produits.stock as stock_actuel',
                    'mouvements_stock.date_peremption',
                    DB::raw('DATEDIFF(mouvements_stock.date_peremption, NOW()) as jours_restants')
                )
                ->orderBy('jours_restants', 'asc')
                ->limit(5)
                ->get();
        }
        
        // Récupérer les notifications pour les alertes
        $notifications = [];
        $alertes = Alerte::where(function($query) use ($activeSuperetteId) {
                $query->where('superette_id', $activeSuperetteId)
                      ->orWhereNull('superette_id');
            })
            ->where('actif', true)
            ->get();
            
        foreach ($alertes as $alerte) {
            switch ($alerte->type) {
                case 'stock':
                    $this->checkStockAlerts($alerte, $notifications);
                    break;
                case 'peremption':
                    $this->checkExpirationAlerts($alerte, $notifications, $activeSuperetteId);
                    break;
                case 'valeur_stock':
                    $this->checkStockValueAlerts($alerte, $notifications);
                    break;
                case 'mouvement':
                    $this->checkMovementAlerts($alerte, $notifications, $activeSuperetteId);
                    break;
            }
        }
        
        return view('dashboard', compact(
            'stats',
            'ventesAujourdhui',
            'caAujourdhui',
            'mouvementsRecents',
            'produitsEnAlerte',
            'activeSuperette',
            'categories',
            'lowStockProducts',
            'expiringProducts',
            'notifications'
        ));
    }

    /**
     * Affiche le tableau de bord administrateur
     *
     * @return \Illuminate\View\View
     */
    public function admin()
    {
        $activeSuperetteId = activeSuperetteId();
        $activeSuperette = activeSuperette();
        
        // Statistiques globales pour les administrateurs
        $stats = [
            'totalProduits' => Produit::where('superette_id', $activeSuperetteId)->count(),
            'produitsEnRupture' => Produit::where('superette_id', $activeSuperetteId)->where('stock', 0)->count(),
            'produitsSousSeuilAlerte' => Produit::where('superette_id', $activeSuperetteId)->whereRaw('stock <= seuil_alerte')->count(),
            'valeurStock' => Produit::where('superette_id', $activeSuperetteId)->sum(DB::raw('stock * prix_achat_ht')),
            'totalSuperettes' => Superette::count(),
            'totalVentes' => Vente::where('superette_id', $activeSuperetteId)->count(),
            'chiffreAffaires' => Vente::where('superette_id', $activeSuperetteId)->whereIn('statut', ['completee', 'terminee'])->sum('montant_total'),
        ];
        
        // Statistiques par superette
        $statsSuperettes = Superette::withCount(['produits', 'ventes'])
            ->withSum('produits', DB::raw('stock * prix_achat_ht'))
            ->withSum('ventes', 'montant_total', function($query) {
                $query->whereIn('statut', ['completee', 'terminee']);
            })
            ->get();
        
        // Renommer les colonnes pour plus de clarté
        $statsSuperettes->each(function($superette) {
            $superette->valeur_stock = $superette->produits_sum_stock_prix_achat_ht;
            $superette->chiffre_affaires = $superette->ventes_sum_montant_total;
        });
        
        // Récupérer les superettes pour le filtre
        $superettes = Superette::orderBy('nom')->get();
        
        // Récupérer les catégories pour le filtre
        $categories = Categorie::orderBy('nom')->get();
        
        // Dernières ventes pour toutes les superettes
        $dernieresVentes = Vente::with(['employe', 'superette'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Derniers mouvements de stock pour toutes les superettes
        $derniersMouvements = MouvementStock::with(['produit', 'utilisateur', 'superette'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('dashboard.admin', compact(
            'stats',
            'statsSuperettes',
            'superettes',
            'categories',
            'dernieresVentes',
            'derniersMouvements',
            'activeSuperette'
        ));
    }

    private function checkStockAlerts($alert, &$notifications)
    {
        $query = DB::table('produits')
            ->where('stock', '<=', $alert->seuil);

        if ($alert->categorie_id) {
            $query->where('categorie_id', $alert->categorie_id);
        }

        $products = $query->get();

        foreach ($products as $product) {
            $notifications[] = [
                'type' => 'stock_bas',
                'message' => "Le stock de {$product->nom} est bas ({$product->stock} unités)",
                'produit_id' => $product->id,
                'alert_id' => $alert->id
            ];
        }
    }

    private function checkExpirationAlerts($alert, &$notifications, $superetteId = null)
    {
        $query = DB::table('mouvements_stock')
            ->join('produits', 'mouvements_stock.produit_id', '=', 'produits.id')
            ->whereNotNull('mouvements_stock.date_peremption')
            ->where('mouvements_stock.date_peremption', '<=', now()->addDays($alert->periode))
            ->where('mouvements_stock.date_peremption', '>', now());

        if ($alert->categorie_id) {
            $query->where('produits.categorie_id', $alert->categorie_id);
        }
        
        // Vérifier si la colonne superette_id existe dans la table mouvements_stock
        if (Schema::hasColumn('mouvements_stock', 'superette_id') && $superetteId) {
            $query->where('mouvements_stock.superette_id', $superetteId);
        }

        $movements = $query->get();

        foreach ($movements as $movement) {
            $notifications[] = [
                'type' => 'peremption',
                'message' => "Le produit {$movement->nom} expire dans {$alert->periode} jours",
                'produit_id' => $movement->produit_id,
                'alert_id' => $alert->id
            ];
        }
    }

    private function checkStockValueAlerts($alert, &$notifications)
    {
        $query = DB::table('produits')
            ->select('produits.*', DB::raw('stock * prix_achat_ht as valeur_stock'));

        if ($alert->categorie_id) {
            $query->where('categorie_id', $alert->categorie_id);
        }

        $products = $query->get();

        foreach ($products as $product) {
            if ($product->valeur_stock >= $alert->seuil) {
                $notifications[] = [
                    'type' => 'valeur_stock',
                    'message' => "La valeur du stock de {$product->nom} est élevée ({$product->valeur_stock} FCFA)",
                    'produit_id' => $product->id,
                    'alert_id' => $alert->id
                ];
            }
        }
    }

    private function checkMovementAlerts($alert, &$notifications, $superetteId = null)
    {
        $dateLimite = now()->subDays($alert->periode);
        $seuil = $alert->seuil;

        $query = DB::table('mouvements_stock')
            ->join('produits', 'mouvements_stock.produit_id', '=', 'produits.id')
            ->where('mouvements_stock.created_at', '>=', $dateLimite);
            
        // Vérifier si la colonne superette_id existe dans la table mouvements_stock
        if (Schema::hasColumn('mouvements_stock', 'superette_id') && $superetteId) {
            $query->where('mouvements_stock.superette_id', $superetteId);
        }
        
        $mouvementsImportants = $query->select('mouvements_stock.produit_id', 'produits.nom', 
                DB::raw('SUM(CASE WHEN type = "entree" THEN quantite_apres_unite ELSE -quantite_apres_unite END) as mouvement_total'))
            ->groupBy('mouvements_stock.produit_id', 'produits.nom')
            ->having('mouvement_total', '>=', $seuil)
            ->get();

        foreach ($mouvementsImportants as $mouvement) {
            $notifications[] = [
                'type' => 'mouvement_important',
                'message' => "Mouvement important détecté pour {$mouvement->nom} : {$mouvement->mouvement_total} unités",
                'produit_id' => $mouvement->produit_id,
                'alert_id' => $alert->id
            ];
        }
    }
} 