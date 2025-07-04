<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Unite;
use App\Models\Fournisseur;
use App\Models\Marque;
use App\Models\MouvementStock;
use App\Models\Caisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Services\ProduitService;
use App\Http\Requests\ProduitRequest;
use Illuminate\Validation\ValidationException;

class ProduitController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected $produitService;

    public function __construct(ProduitService $produitService)
    {
        $this->produitService = $produitService;
        

    }
    
    /**
     * Affiche le formulaire de création d'un nouveau produit.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Vérifier qu'une superette active est sélectionnée
        $activeSuperetteId = session('active_superette_id');
        if (!$activeSuperetteId) {
            return redirect()->route('superettes.select')
                ->with('error', 'Veuillez sélectionner une superette avant de créer un produit.');
        }
        
        $categories = \App\Models\Categorie::orderBy('nom')->get();
        $unites_vente = \App\Models\Unite::orderBy('nom')->get();
        
        return view('produits.create', compact('categories', 'unites_vente'));
    }

    public function index(Request $request)
    {
        $query = Produit::with(['categorie', 'uniteVente', 'mouvementsStock' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Cache des catégories
        $categories = Cache::remember('categories_list', 3600, function() {
            return Categorie::orderBy('nom')->get();
        });

        // Statistiques (calculées à chaque requête pour être immédiates)
        $stats = [
            'totalProducts' => Produit::count(),
            'stockValue' => Produit::sum(DB::raw('stock * prix_achat_ht')),
            'lowStockCount' => Produit::whereRaw('stock <= seuil_alerte')->count(),
            'expiringCount' => Produit::whereNotNull('date_peremption')
                ->where('date_peremption', '<=', now()->addDays(15))
                ->where('date_peremption', '>', now())
                ->count()
        ];

        // Optimisation des filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('code_barres', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('stock <= seuil_alerte');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', 0);
            }
        }

        if ($request->filled('peremption')) {
            $jours = (int) $request->peremption;
            $query->whereNotNull('date_peremption')
                  ->where('date_peremption', '<=', now()->addDays($jours))
                  ->where('date_peremption', '>', now());
        }

        return view('produits.index', [
            'produits' => $query->latest()->paginate(10)->withQueryString(),
            'categories' => $categories,
            'stats' => $stats
        ]);
    }

    /**
     * Enregistre un nouveau produit dans la base de données.
     *
     * @param ProduitRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProduitRequest $request)
    {
        Log::info('Payload produit POST pour création', $request->all());

        // Vérifier qu'une superette active est sélectionnée
        $activeSuperetteId = session('active_superette_id');
        if (!$activeSuperetteId) {
            return redirect()->route('superettes.select')
                ->with('error', 'Veuillez sélectionner une superette avant de créer un produit.');
        }

        try {
            // Obtenir les données validées et préparées depuis la requête
            $produitData = $request->produitData();
            
            // Ajouter explicitement la superette active
            $produitData['superette_id'] = $activeSuperetteId;
            
            // Utiliser le service pour créer le produit
            $produit = $this->produitService->createProduct(
                array_merge($produitData, ["image" => $request->file('image')])
            );
            
            Log::info('Produit créé avec succès', [
                "id" => $produit->id, 
                "nom" => $produit->nom,
                "superette_id" => $produit->superette_id
            ]);
            
            return redirect()->route('produits.index')
                ->with('success', 'Produit "' . $produit->nom . '" créé avec succès !');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du produit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()->withInput()
                ->with('error', 'Erreur lors de la création du produit : ' . $e->getMessage());
        }
    }

    public function edit(Produit $produit)
    {
        \Log::info('Accès à la page d\'édition du produit', ['id' => $produit->id, 'nom' => $produit->nom]);
        try {
            // Chargement des relations nécessaires pour l'édition
            // Utilisation de with() au lieu de load() pour éviter les erreurs si relations nulles
            $produit->load('conditionnements');
            
            \Log::debug('Relations chargées pour édition', [
                'categorie' => $produit->categorie ? $produit->categorie->nom : null,
                'unite' => $produit->uniteVente ? $produit->uniteVente->nom : null,
                'conditionnements' => $produit->conditionnements->count()
            ]);
            
            $categories = Categorie::orderBy('nom')->get();
            $unites_vente = Unite::orderBy('nom')->get();
            $fournisseurs = Fournisseur::orderBy('nom')->get();
            $marques = Marque::orderBy('nom')->get();
            
            return view('produits.edit', compact('produit', 'categories', 'unites_vente', 'fournisseurs', 'marques'));
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'accès à la page d\'édition du produit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'id' => $produit->id
            ]);
            return back()->with('error', 'Erreur lors de l\'accès à la page d\'édition : ' . $e->getMessage());
        }
    }

    /**
     * Met à jour les informations d'un produit existant.
     *
     * @param ProduitRequest $request
     * @param Produit $produit
     * @return \Illuminate\Http\Response
     */
    public function update(ProduitRequest $request, Produit $produit)
    {
        try {
            // Log des données reçues
            \Log::debug('Données reçues pour la mise à jour du produit', [
                'produit_id' => $produit->id,
                'all_data' => $request->all(),
                'conditionnements' => $request->input('conditionnements')
            ]);
            
            // Obtenir les données validées et préparées depuis la requête
            $produitData = $request->produitData();
            
            // Log des données validées
            \Log::debug('Données validées pour la mise à jour du produit', [
                'produit_id' => $produit->id,
                'produitData' => $produitData
            ]);
            
            // Utiliser le service pour mettre à jour le produit
            $produit = $this->produitService->updateProduct(
                $produit,
                array_merge($produitData, ["image" => $request->file('image')])
            );
            
            Log::info('Produit mis à jour avec succès', ["id" => $produit->id, "nom" => $produit->nom]);
            return redirect()->route('produits.show', $produit)
                ->with('success', 'Produit "' . $produit->nom . '" mis à jour avec succès !');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du produit', [
                'id' => $produit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour du produit : ' . $e->getMessage());
        }
    }

    public function destroy(Produit $produit)
    {
        try {
            // Vérifier si le produit a des mouvements de stock ou est dans des ventes
            if ($produit->mouvementsStock()->count() > 0 || $produit->detailVentes()->count() > 0) {
                return redirect()->route('produits.index')->with('error', 'Ce produit ne peut pas être supprimé car il a des mouvements de stock ou est inclus dans des ventes.');
            }

            // Supprimer l'image si elle existe
            if ($produit->image) {
                Storage::disk('public')->delete($produit->image);
            }
            
            $produit->delete();
            \Log::info('Produit supprimé avec succès', ['id' => $produit->id, 'nom' => $produit->nom]);

            return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
             \Log::error('Erreur lors de la suppression du produit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Erreur lors de la suppression du produit : ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        // Cette méthode devrait utiliser une classe d'exportation (ex: Maatwebsite/Excel)
        // Pour l'instant, elle est vide ou contient une logique de base.
         $products = $this->getFilteredQuery($request)->get();
         return (new \App\Exports\ProduitsExport($products))->download('produits.xlsx');
    }

    public function exportPdf(Request $request)
    {
        // Cette méthode devrait utiliser une librairie PDF (ex: Dompdf, Snappy)
        // Pour l'instant, elle est vide ou contient une logique de base.
         $products = $this->getFilteredQuery($request)->get();
         $pdf = \PDF::loadView('exports.products_pdf', compact('products')); // Assurez-vous que la facade PDF est correctement configurée
         return $pdf->download('produits.pdf');
    }

    protected function getFilteredQuery(Request $request)
    {
         $query = Produit::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('code_barres', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('stock <= seuil_alerte');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', 0);
            }
        }

        if ($request->filled('peremption')) {
            $jours = (int) $request->peremption;
            $query->whereNotNull('date_peremption')
                  ->where('date_peremption', '<=', now()->addDays($jours))
                  ->where('date_peremption', '>', now());
        }
        
        return $query;
    }

    public function show(Produit $produit)
    {
        \Log::info('Accès à la page de détail du produit', [
            'id' => $produit->id,
            'nom' => $produit->nom
        ]);
        
        // Forcer le rechargement des conditionnements depuis la base de données
        $conditionnements = \DB::table('conditionnements')
            ->where('produit_id', $produit->id)
            ->get();
            
        \Log::debug('Conditionnements récupérés directement de la DB', [
            'count' => $conditionnements->count(),
            'conditionnements' => $conditionnements->toArray()
        ]);
        
        $produit->load([
            'categorie',
            'uniteVente',
            'mouvementsStock' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'promotions' => function ($query) {
                $query->where('date_debut', '<=', now())
                      ->where('date_fin', '>=', now())
                      ->where('actif', true)
                      ->orderBy('date_fin', 'desc');
            }
        ]);
        
        // Recharger explicitement les conditionnements
        $produit->load('conditionnements');

        // Debug des conditionnements
        \Log::debug('Conditionnements du produit après rechargement', [
            'produit_id' => $produit->id,
            'conditionnements_count' => $produit->conditionnements->count(),
            'conditionnements' => $produit->conditionnements->toArray()
        ]);

        // Utilisation du ProduitService pour obtenir les statistiques
        $stats = $this->produitService->getStatistiquesProduit($produit);

        return view('produits.show', compact('produit', 'stats'));
    }

}