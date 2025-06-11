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
    }

    private function calculerChiffreAffaires($dateDebut, $dateFin)
    {
        return Vente::where('statut', 'terminee')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->sum('montant_total');
    }

    private function calculerNombreVentes($dateDebut, $dateFin)
    {
        return Vente::where('statut', 'terminee')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->count();
    }

    private function calculerValeurStock()
    {
        return Produit::sum(DB::raw('stock * prix_achat_ht'));
    }

    private function calculerMargeBrute($dateDebut, $dateFin)
    {
        return Vente::where('statut', 'terminee')
            ->whereBetween('ventes.created_at', [$dateDebut, $dateFin])
            ->join('detail_ventes', 'ventes.id', '=', 'detail_ventes.vente_id')
            ->sum(DB::raw('(detail_ventes.prix_unitaire - detail_ventes.prix_achat_unitaire) * detail_ventes.quantite'));
    }

    private function donneesRapportVentes($dateDebut, $dateFin)
    {
        // Évolution des ventes
        $evolutionVentes = Vente::where('statut', 'terminee')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(montant_total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Détails des ventes
        $ventes = Vente::with(['details.produit.categorie'])
            ->where('statut', 'terminee')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return [
            'evolutionVentes' => $evolutionVentes,
            'ventes' => $ventes
        ];
    }

    private function donneesRapportStock()
    {
        // Produits en alerte
        $produitsEnAlerte = Produit::where('stock', '<=', DB::raw('seuil_minimum'))
            ->where('stock', '>', 0)
            ->count();

        // Produits en rupture
        $produitsEnRupture = Produit::where('stock', 0)->count();

        // Produits proche de la péremption (30 jours)
        $produitsPerimes = Produit::whereNotNull('date_peremption')
            ->where('date_peremption', '<=', Carbon::now()->addDays(30))
            ->where('stock', '>', 0)
            ->count();

        // Liste des produits proche de la péremption
        $produitsPerimesListe = Produit::whereNotNull('date_peremption')
            ->where('date_peremption', '<=', Carbon::now()->addDays(30))
            ->where('stock', '>', 0)
            ->with('categorie', 'unite')
            ->orderBy('date_peremption')
            ->get();

        // État des stocks
        $stocks = Produit::with(['categorie', 'unite'])
            ->withCount(['mouvements as dernier_mouvement' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('stock')
            ->paginate(10);

        return [
            'produitsEnAlerte' => $produitsEnAlerte,
            'produitsEnRupture' => $produitsEnRupture,
            'produitsPerimes' => $produitsPerimes,
            'produitsPerimesListe' => $produitsPerimesListe,
            'stocks' => $stocks
        ];
    }

    private function donneesRapportMouvements($dateDebut, $dateFin)
    {
        // Évolution des mouvements
        $mouvementsGraphique = MouvementStock::whereBetween('created_at', [$dateDebut, $dateFin])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type = "entree" THEN quantite ELSE 0 END) as entrees'),
                DB::raw('SUM(CASE WHEN type = "sortie" THEN quantite ELSE 0 END) as sorties')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Détails des mouvements
        $mouvements = MouvementStock::with(['produit.categorie', 'user'])
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return [
            'mouvementsGraphique' => $mouvementsGraphique,
            'mouvements' => $mouvements
        ];
    }

    private function donneesRapportCategories($dateDebut, $dateFin)
    {
        // Statistiques par catégorie
        $categories = Categorie::withCount('products')
            ->withSum(['products as valeur_stock' => function($query) {
                $query->select(DB::raw('SUM(stock * prix_achat_ht)'));
            }], 'id')
            ->withSum(['products as ventes_30_jours' => function($query) use ($dateDebut, $dateFin) {
                $query->whereHas('ventes', function($q) use ($dateDebut, $dateFin) {
                    $q->where('statut', 'terminee')
                        ->whereBetween('created_at', [$dateDebut, $dateFin]);
                })->select(DB::raw('SUM(quantite * prix_unitaire)'));
            }], 'id')
            ->withSum(['produits as marge_brute' => function($query) use ($dateDebut, $dateFin) {
                $query->whereHas('ventes', function($q) use ($dateDebut, $dateFin) {
                    $q->where('statut', 'terminee')
                        ->whereBetween('created_at', [$dateDebut, $dateFin]);
                })->select(DB::raw('SUM(quantite * (prix_unitaire - prix_achat_ht))'));
            }], 'id')
            ->get()
            ->map(function($categorie) {
                $categorie->taux_marge = $categorie->ventes_30_jours > 0 
                    ? ($categorie->marge_brute / $categorie->ventes_30_jours) * 100 
                    : 0;
                $categorie->taux_rotation = $categorie->valeur_stock > 0 
                    ? ($categorie->ventes_30_jours / $categorie->valeur_stock) * 12 
                    : 0;
                return $categorie;
            });

        // Top produits par catégorie
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

        return [
            'categories' => $categories,
            'topProduitsParCategorie' => $topProduitsParCategorie
        ];
    }

    public function categories()
    {
        $categories = Categorie::withCount('products')
            ->orderBy('produits_count', 'desc')
            ->get();

        return view('reports.categories', compact('categories'));
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
    }
} 