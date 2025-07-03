<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Définir un type par défaut si non fourni
            if (!$request->has('type')) {
                $request->merge(['type' => 'ventes']);
            }
            $request->validate([
                'date_debut' => 'nullable|date',
                'date_fin' => 'nullable|date|after_or_equal:date_debut',
                'type' => 'required|in:ventes,stock,mouvements,categories'
            ]);

            $dateDebut = $request->date_debut ? Carbon::parse($request->date_debut) : Carbon::now()->startOfMonth();
            $dateFin = $request->date_fin ? Carbon::parse($request->date_fin) : Carbon::now();

            // Statistiques générales
            $stats = [
                'chiffre_affaires' => $this->calculerChiffreAffaires($dateDebut, $dateFin),
                'nombre_ventes' => $this->calculerNombreVentes($dateDebut, $dateFin),
                'valeur_stock' => $this->calculerValeurStock(),
                'marge_brute' => $this->calculerMargeBrute($dateDebut, $dateFin)
            ];

            // Données spécifiques selon le type de rapport
            $donnees = match($request->type) {
                'ventes' => $this->donneesRapportVentes($dateDebut, $dateFin),
                'stock' => $this->donneesRapportStock(),
                'mouvements' => $this->donneesRapportMouvements($dateDebut, $dateFin),
                'categories' => $this->donneesRapportCategories($dateDebut, $dateFin),
                default => []
            };

            return view('reports.index', array_merge($stats, $donnees, [
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'type' => $request->type
            ]));
        } catch (\Throwable $e) {
            // Logger l'erreur
            \Illuminate\Support\Facades\Log::error("Erreur dans ReportController::index: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            
            // Rediriger vers une page d'erreur ou afficher un message d'erreur
            return view('reports.index', [
                'error_message' => "Une erreur est survenue lors du chargement du rapport: " . $e->getMessage(),
                'date_debut' => Carbon::now()->startOfMonth(),
                'date_fin' => Carbon::now(),
                'type' => $request->type ?? 'ventes',
                'chiffre_affaires' => 0,
                'nombre_ventes' => 0,
                'valeur_stock' => 0,
                'marge_brute' => 0,
                'ventes' => collect(),
                'mouvements' => collect(),
                'stocks' => collect(),
                'evolutionVentes' => collect(),
                'categories' => collect(),
            ]);
        }
    }

    private function calculerChiffreAffaires($dateDebut, $dateFin)
    {
        $superetteId = session('active_superette_id');
        $query = Vente::where('statut', 'terminee')
            ->whereBetween(DB::raw('COALESCE(date_vente, created_at)'), [$dateDebut, $dateFin]);
            
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        return $query->sum('montant_total');
    }

    private function calculerNombreVentes($dateDebut, $dateFin)
    {
        $superetteId = session('active_superette_id');
        $query = Vente::where('statut', 'terminee')
            ->whereBetween(DB::raw('COALESCE(date_vente, created_at)'), [$dateDebut, $dateFin]);
            
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        return $query->count();
    }

    private function calculerValeurStock()
    {
        $superetteId = session('active_superette_id');
        $query = Produit::query();
        
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        return $query->sum(DB::raw('stock * prix_achat_ht'));
    }

    private function calculerMargeBrute($dateDebut, $dateFin)
    {
        $superetteId = session('active_superette_id');
        $query = Vente::where('statut', 'terminee')
            ->whereBetween(DB::raw('COALESCE(ventes.date_vente, ventes.created_at)'), [$dateDebut, $dateFin])
            ->join('detail_ventes', 'ventes.id', '=', 'detail_ventes.vente_id');
            
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $query->where('ventes.superette_id', $superetteId);
        }
        
        return $query->sum(DB::raw('(detail_ventes.prix_unitaire - COALESCE(detail_ventes.prix_achat_unitaire, 0)) * detail_ventes.quantite'));
    }

    private function donneesRapportVentes($dateDebut, $dateFin)
    {
        try {
            \Illuminate\Support\Facades\Log::info("Génération rapport ventes du {$dateDebut} au {$dateFin}");
            
            $superetteId = session('active_superette_id');
            
            // Évolution des ventes - données optimisées
            $evolutionQuery = Vente::where('statut', 'terminee')
                ->whereBetween(DB::raw('COALESCE(date_vente, created_at)'), [$dateDebut->startOfDay(), $dateFin->endOfDay()]);
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $evolutionQuery->where('superette_id', $superetteId);
            }
            
            $evolutionVentes = $evolutionQuery->selectRaw('DATE(COALESCE(date_vente, created_at)) as date, SUM(montant_total) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function($item) {
                    return [
                        'date' => Carbon::parse($item->date)->format('d/m/Y'),
                        'total' => (float) $item->total
                    ];
                });
            
            \Illuminate\Support\Facades\Log::info("Nombre de jours avec ventes: " . $evolutionVentes->count());
            
            // Top produits vendus
            $topProduitsQuery = DB::table('detail_ventes')
                ->join('produits', 'detail_ventes.produit_id', '=', 'produits.id')
                ->join('ventes', 'detail_ventes.vente_id', '=', 'ventes.id')
                ->whereBetween(DB::raw('COALESCE(ventes.date_vente, ventes.created_at)'), [$dateDebut->startOfDay(), $dateFin->endOfDay()])
                ->where('ventes.statut', 'terminee');
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $topProduitsQuery->where('ventes.superette_id', $superetteId);
            }
            
            $topProduits = $topProduitsQuery->selectRaw('produits.id, produits.nom, SUM(detail_ventes.quantite) as quantite, SUM(detail_ventes.sous_total) as total')
                ->groupBy('produits.id', 'produits.nom')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            // Détails des ventes
            $ventesQuery = Vente::with(['client'])
                ->withCount('details')
                ->where('statut', 'terminee')
                ->whereBetween(DB::raw('COALESCE(date_vente, created_at)'), [$dateDebut->startOfDay(), $dateFin->endOfDay()]);
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $ventesQuery->where('superette_id', $superetteId);
            }
            
            $ventes = $ventesQuery->orderBy(DB::raw('COALESCE(date_vente, created_at)'), 'desc')
                ->paginate(15);

            return [
                'evolutionVentes' => $evolutionVentes,
                'topProduits' => $topProduits,
                'ventes' => $ventes
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur dans donneesRapportVentes: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return [
                'evolutionVentes' => collect(),
                'topProduits' => collect(),
                'ventes' => collect()
            ];
        }
    }

    private function donneesRapportStock()
    {
        try {
            $superetteId = session('active_superette_id');
            
            // Produits en alerte
            $produitsEnAlerteQuery = Produit::where('stock', '<=', DB::raw('seuil_alerte'))
                ->where('stock', '>', 0);
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $produitsEnAlerteQuery->where('superette_id', $superetteId);
            }
            
            $produitsEnAlerte = $produitsEnAlerteQuery->count();

            // Produits en rupture
            $produitsEnRuptureQuery = Produit::where('stock', '<=', 0);
            
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $produitsEnRuptureQuery->where('superette_id', $superetteId);
            }
            
            $produitsEnRupture = $produitsEnRuptureQuery->count();

            // Produits proche de la péremption (30 jours)
            $produitsPerimesQuery = Produit::whereNotNull('date_peremption')
                ->where('date_peremption', '<=', Carbon::now()->addDays(30))
                ->where('stock', '>', 0);
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $produitsPerimesQuery->where('superette_id', $superetteId);
            }
            
            $produitsPerimes = $produitsPerimesQuery->count();

            // Liste des produits proche de la péremption
            $produitsPerimesListeQuery = Produit::whereNotNull('date_peremption')
                ->where('date_peremption', '<=', Carbon::now()->addDays(30))
                ->where('stock', '>', 0)
                ->with(['categorie', 'uniteVente']);
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $produitsPerimesListeQuery->where('superette_id', $superetteId);
            }
            
            $produitsPerimesListe = $produitsPerimesListeQuery->orderBy('date_peremption')
                ->limit(10)
                ->get();

            // État des stocks
            $stocksQuery = Produit::with(['categorie', 'uniteVente']);
            
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $stocksQuery->where('superette_id', $superetteId);
            }
            
            $stocks = $stocksQuery->orderBy(DB::raw('stock / NULLIF(seuil_alerte, 0)'))
                ->paginate(15);

            // Statistiques de valorisation du stock
            $stockStatsQuery = DB::table('produits');
            
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $stockStatsQuery->where('superette_id', $superetteId);
            }
            
            $stockStats = $stockStatsQuery->select(
                    DB::raw('SUM(stock * prix_achat_ht) as valeur_achat'),
                    DB::raw('SUM(stock * prix_vente_ttc) as valeur_vente'),
                    DB::raw('SUM(IF(stock <= seuil_alerte, stock * prix_achat_ht, 0)) as valeur_alerte'),
                    DB::raw('COUNT(*) as total_produits')
                )
                ->first();

            return [
                'produitsEnAlerte' => $produitsEnAlerte,
                'produitsEnRupture' => $produitsEnRupture,
                'produitsPerimes' => $produitsPerimes,
                'produitsPerimesListe' => $produitsPerimesListe,
                'stocks' => $stocks,
                'stockStats' => $stockStats
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur dans donneesRapportStock: " . $e->getMessage());
            return [
                'produitsEnAlerte' => 0,
                'produitsEnRupture' => 0,
                'produitsPerimes' => 0,
                'produitsPerimesListe' => collect(),
                'stocks' => collect(),
                'stockStats' => (object)[
                    'valeur_achat' => 0,
                    'valeur_vente' => 0,
                    'valeur_alerte' => 0,
                    'total_produits' => 0
                ]
            ];
        }
    }

    private function donneesRapportMouvements($dateDebut, $dateFin)
    {
        try {
            \Illuminate\Support\Facades\Log::info("Génération rapport mouvements du {$dateDebut} au {$dateFin}");
            
            $superetteId = session('active_superette_id');
            
            // Récupération des dates pour le graphique
            $dateRange = $this->generateDateRange($dateDebut, $dateFin);
            
            // Évolution des mouvements
            $mouvementsDataQuery = MouvementStock::selectRaw("
                DATE(COALESCE(date_mouvement, created_at)) as date,
                SUM(CASE 
                    WHEN type IN ('entree', 'ajustement_positif') 
                    THEN (quantite_apres_conditionnement - quantite_avant_conditionnement)
                    ELSE 0 
                END) as entrees,
                SUM(CASE 
                    WHEN type IN ('sortie', 'ajustement_negatif') 
                    THEN ABS(quantite_avant_conditionnement - quantite_apres_conditionnement)
                    ELSE 0 
                END) as sorties
            ")
            ->whereBetween(DB::raw('COALESCE(date_mouvement, created_at)'), [$dateDebut, $dateFin]);
            
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $mouvementsDataQuery->where('superette_id', $superetteId);
            }
            
            $mouvementsData = $mouvementsDataQuery->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            \Illuminate\Support\Facades\Log::info("Nombre de jours avec mouvements: " . $mouvementsData->count());
            
            // Compléter avec des jours sans mouvement
            $mouvementsGraphique = collect();
            foreach ($dateRange as $date) {
                $formattedDate = $date->format('Y-m-d');
                if (isset($mouvementsData[$formattedDate])) {
                    $mouvementsGraphique->push([
                        'date' => $date->format('d/m/Y'),
                        'entrees' => (float) $mouvementsData[$formattedDate]->entrees,
                        'sorties' => (float) $mouvementsData[$formattedDate]->sorties
                    ]);
                } else {
                    $mouvementsGraphique->push([
                        'date' => $date->format('d/m/Y'),
                        'entrees' => 0,
                        'sorties' => 0
                    ]);
                }
            }

            // Détails des mouvements
            $mouvementsQuery = MouvementStock::with(['produit' => function($query) {
                    $query->withTrashed()->with(['categorie', 'uniteVente']);
                }, 'user'])
                ->whereBetween(DB::raw('COALESCE(date_mouvement, created_at)'), [$dateDebut->startOfDay(), $dateFin->endOfDay()]);
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $mouvementsQuery->where('superette_id', $superetteId);
            }
            
            $mouvements = $mouvementsQuery->orderBy(DB::raw('COALESCE(date_mouvement, created_at)'), 'desc')
                ->paginate(15);

            return [
                'mouvementsGraphique' => $mouvementsGraphique,
                'mouvements' => $mouvements
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur dans donneesRapportMouvements: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return [
                'mouvementsGraphique' => collect(),
                'mouvements' => collect()
            ];
        }
    }
    
    /**
     * Génère une séquence de dates entre deux dates
     */
    private function generateDateRange($dateDebut, $dateFin)
    {
        $dates = collect();
        $currentDate = clone $dateDebut;
        
        while ($currentDate->lte($dateFin)) {
            $dates->push(clone $currentDate);
            $currentDate->addDay();
        }
        
        return $dates;
    }

    private function donneesRapportCategories($dateDebut, $dateFin)
    {
        try {
            \Illuminate\Support\Facades\Log::info("Génération rapport catégories du {$dateDebut} au {$dateFin}");
            
            // Statistiques par catégorie avec une approche plus robuste
            $categories = Categorie::select(
                    'categories.id',
                    'categories.nom',
                    DB::raw('COUNT(DISTINCT produits.id) as nb_produits'),
                    DB::raw('COALESCE(SUM(detail_ventes.quantite), 0) as quantite_vendue'),
                    DB::raw('COALESCE(SUM(detail_ventes.sous_total), 0) as total')
                )
                ->leftJoin('produits', 'categories.id', '=', 'produits.categorie_id')
                ->leftJoin('detail_ventes', 'produits.id', '=', 'detail_ventes.produit_id')
                ->leftJoin('ventes', function ($join) use ($dateDebut, $dateFin) {
                    $join->on('detail_ventes.vente_id', '=', 'ventes.id')
                        ->where('ventes.statut', '=', 'terminee')
                        ->whereBetween(DB::raw('COALESCE(ventes.date_vente, ventes.created_at)'), [$dateDebut, $dateFin]);
                })
                ->groupBy('categories.id', 'categories.nom')
                ->orderBy('total', 'desc')
                ->get();
            
            \Illuminate\Support\Facades\Log::info("Nombre de catégories trouvées: " . $categories->count());
            
            // Top produits par catégorie
            $topProduitsParCategorie = [];
            
            // Récupérer les 3 meilleures catégories
            $topCategoriesIds = $categories->take(5)->pluck('id');
            
            if ($topCategoriesIds->isNotEmpty()) {
                foreach ($topCategoriesIds as $categorieId) {
                    $categorie = Categorie::find($categorieId);
                    
                    if ($categorie) {
                        $topProduits = Produit::select(
                                'produits.id',
                                'produits.nom',
                                DB::raw('COALESCE(SUM(detail_ventes.sous_total), 0) as total')
                            )
                            ->where('produits.categorie_id', $categorieId)
                            ->leftJoin('detail_ventes', 'produits.id', '=', 'detail_ventes.produit_id')
                            ->leftJoin('ventes', function ($join) use ($dateDebut, $dateFin) {
                                $join->on('detail_ventes.vente_id', '=', 'ventes.id')
                                    ->where('ventes.statut', '=', 'terminee')
                                    ->whereBetween(DB::raw('COALESCE(ventes.date_vente, ventes.created_at)'), [$dateDebut, $dateFin]);
                            })
                            ->groupBy('produits.id', 'produits.nom')
                            ->orderBy('total', 'desc')
                            ->limit(5)
                            ->get();
                        
                        if ($topProduits->isNotEmpty()) {
                            $topProduitsParCategorie[$categorie->nom] = $topProduits;
                        }
                    }
                }
            }

            return [
                'categories' => $categories,
                'topProduitsParCategorie' => $topProduitsParCategorie
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur dans donneesRapportCategories: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return [
                'categories' => collect(),
                'topProduitsParCategorie' => []
            ];
        }
    }

    public function categories()
    {
        $categories = Categorie::withCount('products')
            ->orderBy('produits_count', 'desc')
            ->get();

        return view('reports.categories', compact('categories'));
    }
    
    public function stock()
    {
        $dateDebut = request('date_debut', now()->startOfMonth());
        $dateFin = request('date_fin', now()->endOfMonth());
        
        // Statistiques générales
        $stats = [
            'chiffre_affaires' => $this->calculerChiffreAffaires($dateDebut, $dateFin),
            'nombre_ventes' => $this->calculerNombreVentes($dateDebut, $dateFin),
            'valeur_stock' => $this->calculerValeurStock(),
            'marge_brute' => $this->calculerMargeBrute($dateDebut, $dateFin)
        ];
        
        $donnees = $this->donneesRapportStock();
        
        return view('reports.stock', array_merge($stats, $donnees, [
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'type' => 'stock'
        ]));
    }
    
    public function movements()
    {
        $dateDebut = request('date_debut', now()->startOfMonth());
        $dateFin = request('date_fin', now()->endOfMonth());
        
        // Statistiques générales
        $stats = [
            'chiffre_affaires' => $this->calculerChiffreAffaires($dateDebut, $dateFin),
            'nombre_ventes' => $this->calculerNombreVentes($dateDebut, $dateFin),
            'valeur_stock' => $this->calculerValeurStock(),
            'marge_brute' => $this->calculerMargeBrute($dateDebut, $dateFin)
        ];
        
        $donnees = $this->donneesRapportMouvements($dateDebut, $dateFin);
        
        return view('reports.movements', array_merge($stats, $donnees, [
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'type' => 'mouvements'
        ]));
    }

    public function export()
    {
        $dateDebut = request('date_debut', now()->startOfMonth());
        $dateFin = request('date_fin', now()->endOfMonth());

        $topProduitsParCategorie = Categorie::with(['produits' => function($query) use ($dateDebut, $dateFin) {
            $query->withCount(['ventes as quantite_vendue' => function($q) use ($dateDebut, $dateFin) {
                $q->where('statut', 'terminee')
                    ->whereBetween('created_at', [$dateDebut, $dateFin]);
            }])
            ->withSum(['ventes as ventes_30_jours' => function($q) use ($dateDebut, $dateFin) {
                $q->where('statut', 'terminee')
                    ->whereBetween('created_at', [$dateDebut, $dateFin]);
            }], DB::raw('quantite * prix_unitaire'))
            ->orderByDesc('ventes_30_jours')
            ->limit(5);
        }])
        ->get()
        ->pluck('products', 'nom');
        
        // Retourner une vue pour l'export
        return view('reports.export', compact('topProduitsParCategorie', 'dateDebut', 'dateFin'));
    }
}