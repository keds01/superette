<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\StockMovement;
use App\Services\StockService;
// Correction : on n'utilise pas Product ni Category, mais bien Produit et Categorie.
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    protected $stockService;
    
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }
    
    public function index(Request $request)
    {
        $query = Produit::with([
            'categorie',
            'uniteVente',
            'mouvementsStock' => function($q) {
                $q->latest()->limit(1);
            },
            'promotions' => function($q) {
                $q->where('actif', true)
                  ->where('date_debut', '<=', now())
                  ->where('date_fin', '>=', now());
            }
        ]);

        // Filtres
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
            } elseif ($request->stock_status === 'expiring') {
                $query->whereNotNull('date_peremption')
                    ->where('date_peremption', '<=', now()->addDays(15));
            }
        }

        // Récupération des catégories pour le filtre
        $categories = Categorie::orderBy('nom')->get();
        
        // Statistiques globales
        $totalProducts = Produit::count();
        $stockValue = Produit::sum(DB::raw('stock * prix_achat_ht'));
        $lowStockCount = Produit::whereRaw('stock <= seuil_alerte')->count();
        $expiringCount = Produit::whereNotNull('date_peremption')
            ->where('date_peremption', '<=', now()->addDays(15))
            ->where('date_peremption', '>', now())
            ->count();
        
        $products = $query->latest()->paginate(10)->withQueryString();

        return view('stocks.index', compact(
            'products',
            'categories',
            'totalProducts',
            'stockValue',
            'lowStockCount',
            'expiringCount'
        ));
    }
    
    public function inventaire()
    {
        $categories = Categorie::orderBy('nom')->get();
        return view('stocks.inventaire', compact('categories'));
    }
    
    public function getProductsForInventaire(Request $request)
    {
        $query = Produit::with(['categorie', 'uniteVente']);
        
        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('code_barres', 'like', "%{$search}%");
            });
        }
        
        $products = $query->get()->map(function($product) {
            // Conversion du stock en cartons + unités
            $stockConversion = $this->stockService->convertFromUnits(
                $product->stock,
                $product->quantite_par_conditionnement
            );
            
            return [
                'id' => $product->id,
                'nom' => $product->nom,
                'reference' => $product->reference,
                'code_barres' => $product->code_barres,
                'categorie' => $product->categorie->nom,
                'stock_theorique' => $product->stock,
                'cartons_theoriques' => $stockConversion['cartons'],
                'unites_theoriques' => $stockConversion['unites'],
                'unite_vente' => $product->uniteVente->nom,
                'symbole_unite' => $product->uniteVente->symbole,
                'quantite_par_conditionnement' => $product->quantite_par_conditionnement,
                'conditionnement_fournisseur' => $product->conditionnement_fournisseur,
                'cartons_reels' => 0,
                'unites_reelles' => 0,
                'stock_reel' => 0,
                'ecart' => 0,
                'valeur_ecart' => 0,
                'prix_achat_ht' => $product->prix_achat_ht
            ];
        });
        
        return response()->json($products);
    }
    
    public function ajusterStock(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id', // Correction ici
            'cartons_reels' => 'required|integer|min:0',
            'unites_reelles' => 'required|numeric|min:0',
            'motif' => 'required|string|max:255'
        ]);
        
        $product = Produit::findOrFail($request->produit_id);
        
        try {
            $movement = $this->stockService->adjustStock(
                $product,
                $request->cartons_reels,
                $request->unites_reelles,
                $request->motif
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Stock ajusté avec succès',
                'movement' => $movement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajustement du stock: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function historique(Request $request)
    {
        $query = StockMovement::with(['produit', 'produit.uniteVente']);
        
        if ($request->filled('produit_id')) {
            $query->where('produit_id', $request->produit_id);
        } // Correction ici (produit_id au lieu de product_id)
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->date_debut)->startOfDay(),
                Carbon::parse($request->date_fin)->endOfDay()
            ]);
        }
        
        $movements = $query->latest('created_at')->paginate(15)->withQueryString();
        $products = Produit::orderBy('nom')->get();
        
        return view('stocks.historique', compact('movements', 'products'));
    }
    
    public function ajoutStock()
    {
        $products = Produit::orderBy('nom')->get();
        return view('stocks.ajout', compact('products'));
    }
    
    public function retraitStock()
    {
        $products = Produit::orderBy('nom')->get();
        return view('stocks.retrait', compact('products'));
    }
    
    public function processAjoutStock(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'cartons' => 'required|integer|min:0',
            'unites' => 'required|numeric|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'motif' => 'required|string|max:255',
            'date_peremption' => 'nullable|date|after:today'
        ]);
        
        $product = Produit::findOrFail($request->produit_id);
        
        try {
            $movement = $this->stockService->addStock(
                $product,
                $request->cartons,
                $request->unites,
                $request->motif,
                $request->prix_unitaire,
                $request->date_peremption
            );
            
            return redirect()
                ->route('stocks.historique')
                ->with('success', 'Stock ajouté avec succès');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout de stock: ' . $e->getMessage());
        }
    }
    
    public function processRetraitStock(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'cartons' => 'required|integer|min:0',
            'unites' => 'required|numeric|min:0',
            'motif' => 'required|string|max:255'
        ]);
        
        $product = Produit::findOrFail($request->produit_id);
        
        try {
            $movement = $this->stockService->removeStock(
                $product,
                $request->cartons,
                $request->unites,
                $request->motif
            );
            
            if (!$movement) {
                return back()
                    ->withInput()
                    ->with('error', 'Stock insuffisant pour effectuer ce retrait');
            }
            
            return redirect()
                ->route('stocks.historique')
                ->with('success', 'Stock retiré avec succès');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors du retrait de stock: ' . $e->getMessage());
        }
    }
}