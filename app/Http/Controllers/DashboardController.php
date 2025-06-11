<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Alerte;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filtres
        $dateDebut = $request->get('date_debut', now()->subDays(30)->format('Y-m-d'));
        $dateFin = $request->get('date_fin', now()->format('Y-m-d'));
        $categorieId = $request->get('categorie');

        // Statistiques générales
        $query = Produit::with(['categorie', 'uniteVente']);
        if ($categorieId) {
            $query->where('categorie_id', $categorieId);
        }
        $produits = $query->get();

        $stockValue = $produits->sum(function ($produit) {
            return $produit->stock * $produit->prix_achat_ht;
        });
        $totalProducts = $produits->count();

        // Produits en alerte
        $lowStockProducts = $produits->filter(function ($produit) {
            return $produit->stock <= $produit->seuil_alerte;
        });
        $lowStockCount = $lowStockProducts->count();

        // Produits à péremption
        $expiringProducts = Produit::whereHas('mouvementsStock', function ($query) use ($dateDebut, $dateFin) {
            $query->whereNotNull('date_peremption')
                ->where('date_peremption', '<=', Carbon::parse($dateFin))
                ->where('date_peremption', '>', now());
        })->with(['categorie', 'uniteVente', 'mouvementsStock' => function ($query) use ($dateDebut, $dateFin) {
            $query->whereNotNull('date_peremption')
                ->where('date_peremption', '<=', Carbon::parse($dateFin))
                ->where('date_peremption', '>', now());
        }])->get();
        $expiringCount = $expiringProducts->count();

        // Données pour le graphique des mouvements
        $period = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin));
        $dates = collect(range($period, 0))->map(function ($day) use ($dateFin) {
            return Carbon::parse($dateFin)->subDays($day)->format('Y-m-d');
        });

        $movementsChart = [
            'labels' => $dates->map(function ($date) {
                return Carbon::parse($date)->format('d/m');
            }),
            'entrees' => $dates->map(function ($date) use ($categorieId) {
                $query = MouvementStock::where('type', 'entree')
                    ->whereDate('created_at', $date);
                
                if ($categorieId) {
                    $query->whereHas('produit', function ($q) use ($categorieId) {
                        $q->where('categorie_id', $categorieId);
                    });
                }
                
                return $query->sum('quantite_apres_unite');
            }),
            'sorties' => $dates->map(function ($date) use ($categorieId) {
                $query = MouvementStock::where('type', 'sortie')
                    ->whereDate('created_at', $date);
                
                if ($categorieId) {
                    $query->whereHas('produit', function ($q) use ($categorieId) {
                        $q->where('categorie_id', $categorieId);
                    });
                }
                
                return $query->sum('quantite_apres_unite');
            })
        ];

        // Données pour le graphique des catégories
        $categories = Categorie::withCount('products')
            ->withSum('products', 'stock')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();

        $categoriesChart = [
            'labels' => $categories->pluck('nom'),
            'data' => $categories->map(function ($category) {
                return $category->products->sum(function ($produit) {
                    return $produit->stock * $produit->prix_achat_ht;
                });
            })
        ];

        // Alertes actives
        $alerts = Alerte::where('actif', true)
            ->with('categorie')
            ->get();

        // Vérification des alertes
        $notifications = [];
        foreach ($alerts as $alert) {
            switch ($alert->type) {
                case 'stock_bas':
                    $this->checkStockAlerts($alert, $notifications);
                    break;
                case 'peremption':
                    $this->checkExpirationAlerts($alert, $notifications);
                    break;
                case 'valeur_stock':
                    $this->checkStockValueAlerts($alert, $notifications);
                    break;
                case 'mouvement_important':
                    $this->checkMovementAlerts($alert, $notifications);
                    break;
            }
        }

        // Liste des catégories pour les filtres
        $categories = Categorie::orderBy('nom')->get();

        return view('dashboard', compact(
            'stockValue',
            'totalProducts',
            'lowStockProducts',
            'lowStockCount',
            'expiringProducts',
            'expiringCount',
            'movementsChart',
            'categoriesChart',
            'notifications',
            'categories'
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

    private function checkExpirationAlerts($alert, &$notifications)
    {
        $query = DB::table('mouvements_stock')
            ->join('produits', 'mouvements_stock.produit_id', '=', 'produits.id')
            ->whereNotNull('mouvements_stock.date_peremption')
            ->where('mouvements_stock.date_peremption', '<=', now()->addDays($alert->periode))
            ->where('mouvements_stock.date_peremption', '>', now());

        if ($alert->categorie_id) {
            $query->where('produits.categorie_id', $alert->categorie_id);
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

    private function checkMovementAlerts($alert, &$notifications)
    {
        $dateLimite = now()->subDays($alert->periode);
        $seuil = $alert->seuil;

        $mouvementsImportants = DB::table('mouvements_stock')
            ->join('produits', 'mouvements_stock.produit_id', '=', 'produits.id')
            ->where('mouvements_stock.created_at', '>=', $dateLimite)
            ->select('mouvements_stock.produit_id', 'produits.nom', 
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