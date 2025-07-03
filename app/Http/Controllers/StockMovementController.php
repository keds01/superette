<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\Unite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class StockMovementController extends Controller
{
    /**
     * Récupère les détails d'un produit et son historique pour affichage AJAX
     *
     * @param int $id ID du produit
     * @return JsonResponse
     */
    public function getProductDetails($id): JsonResponse
    {
        try {
            // Récupération du produit avec ses relations
            $produit = Produit::with(['categorie', 'uniteVente'])
                ->findOrFail($id);
            
            // Récupération des 3 derniers mouvements
            $recentMouvements = MouvementStock::where('produit_id', $id)
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function($mouvement) {
                    return [
                        'id' => $mouvement->id,
                        'type' => $mouvement->type,
                        'quantite' => $mouvement->quantite_apres_unite - $mouvement->quantite_avant_unite,
                        'date' => $mouvement->created_at->format('d/m/Y H:i'),
                        'motif' => $mouvement->motif,
                        'user' => $mouvement->user ? $mouvement->user->name : 'Système'
                    ];
                });
            
            // Construction du résumé du produit
            $productSummary = [
                'nom' => $produit->nom,
                'categorie' => $produit->categorie ? $produit->categorie->nom : 'Non catégorisé',
                'stock' => $produit->stock,
                'unite' => $produit->uniteVente ? $produit->uniteVente->symbole : '',
                'prix_achat' => $produit->prix_achat_ht,
                'prix_vente' => $produit->prix_vente_ht,
                'prix_vente_ttc' => $produit->prix_vente_ttc,
                'seuil_alerte' => $produit->seuil_alerte
            ];
            
            return response()->json([
                'success' => true,
                'produit' => $productSummary,
                'mouvements' => $recentMouvements,
                'historique_existe' => $recentMouvements->count() > 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $mouvements = MouvementStock::with(['produit.categorie', 'produit.uniteVente'])
            ->latest()
            ->paginate(15);

        // Statistiques pour l'en-tête
        $entreesCount = MouvementStock::where('type', 'entree')->count();
        $sortiesCount = MouvementStock::where('type', 'sortie')->count();
        $produitsCount = MouvementStock::distinct('produit_id')->count('produit_id');

        return view('stock-movements.index', compact('mouvements', 'entreesCount', 'sortiesCount', 'produitsCount'));
    }

    public function create(Request $request)
    {
        $products = Produit::with(['categorie', 'uniteVente'])
            ->orderBy('nom')
            ->get();

        $selectedProductId = $request->query('product_id');
        $selectedProduct = null;
        $lastMovements = collect();

        if ($selectedProductId) {
            $selectedProduct = Produit::with(['categorie', 'uniteVente'])
                ->find($selectedProductId);
            if ($selectedProduct) {
                $lastMovements = $selectedProduct->mouvementsStock()
                    ->latest('created_at')
                    ->limit(3)
                    ->get();
            }
        }

        return view('stock-movements.create', compact('products', 'selectedProductId', 'selectedProduct', 'lastMovements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'type' => 'required|in:entree,sortie,ajustement_positif,ajustement_negatif',
            'quantite' => 'required|numeric|gt:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'motif' => 'required|string|max:255',
            'date_peremption' => 'nullable|date',
            'date_mouvement' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $product = Produit::findOrFail($validated['produit_id']);
            $movementType = $validated['type'];
            $quantity = $validated['quantite'];
            $stockAvant = $product->stock;

            // Vérification de stock suffisant pour les sorties
            if (in_array($movementType, ['sortie', 'ajustement_negatif'])) {
                if ($product->stock < $quantity) {
                    throw new \Exception('Stock insuffisant pour effectuer ce mouvement.');
                }
            }

            // Calcul du nouveau stock selon le type de mouvement
            $stockApres = $stockAvant;
            switch ($movementType) {
                case 'entree':
                case 'ajustement_positif':
                    $stockApres = $stockAvant + $quantity;
                    break;
                case 'sortie':
                case 'ajustement_negatif':
                    $stockApres = $stockAvant - $quantity;
                    break;
            }

            // Création du mouvement avec les quantités avant/après correctes
            $movementData = [
                'produit_id' => $product->id,
                'type' => $movementType,
                'quantite_avant_conditionnement' => $stockAvant,
                'quantite_apres_conditionnement' => $stockApres,
                'quantite_avant_unite' => $stockAvant,
                'quantite_apres_unite' => $stockApres,
                'prix_unitaire' => $validated['prix_unitaire'],
                'motif' => $validated['motif'],
                'date_peremption' => $validated['date_peremption'] ?? null,
                'user_id' => null, // Plus d'utilisateur associé
                'date_mouvement' => $validated['date_mouvement']
            ];

            $mouvement = MouvementStock::create($movementData);

            // Mise à jour du stock du produit
            $product->stock = $stockApres;
            
            // Si c'est une entrée, on peut éventuellement mettre à jour le prix d'achat
            if ($movementType === 'entree' && $validated['prix_unitaire'] != $product->prix_achat_ht) {
                // On pourrait implémenter ici une logique de prix moyen pondéré
                // Pour l'instant, on utilise simplement le nouveau prix
                $product->prix_achat_ht = $validated['prix_unitaire'];
            }

            $product->save();

            DB::commit();

            return redirect()
                ->route('mouvements-stock.index')
                ->with('success', 'Mouvement de stock enregistré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'enregistrement du mouvement de stock : ' . $e->getMessage(), ['exception' => $e]);
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement du mouvement : ' . $e->getMessage());
        }
    }

    public function show(MouvementStock $stockMovement)
    {
        \Illuminate\Support\Facades\Log::info('--- Debug Mouvement Stock ---');
        \Illuminate\Support\Facades\Log::info('Mouvement ID: ' . $stockMovement->id);
        \Illuminate\Support\Facades\Log::info('Produit ID from Mouvement: ' . $stockMovement->produit_id);

        $produitManuellement = \App\Models\Produit::withTrashed()->find($stockMovement->produit_id);
        \Illuminate\Support\Facades\Log::info('Produit trouvé manuellement: ' . ($produitManuellement ? 'OUI' : 'NON'));

        // Si le produit est trouvé manuellement mais pas via la relation, on l'attribue directement
        if ($produitManuellement) {
            // Forcer l'attribution du produit à la relation
            $stockMovement->produit = $produitManuellement;
            
            // Forcer le chargement des relations du produit
            if ($produitManuellement->categorie_id) {
                $produitManuellement->load('categorie');
            }
            
            if ($produitManuellement->unite_vente_id) {
                $produitManuellement->load('uniteVente');
            }
            
            \Illuminate\Support\Facades\Log::info('Produit assigné manuellement: ' . $produitManuellement->nom);
        } else {
            // On doit recharger la relation car un accès précédent pourrait l'avoir mise en cache à null
            $stockMovement->load('produit');
            $produitViaRelation = $stockMovement->produit;
            \Illuminate\Support\Facades\Log::info('Produit via relation est null: ' . (is_null($produitViaRelation) ? 'OUI' : 'NON'));

            if (is_null($produitViaRelation)) {
                \Illuminate\Support\Facades\Log::warning('!!! Incohérence: Le produit ID ' . $stockMovement->produit_id . ' n\'a pas été trouvé.');
            }
        }

        // On charge les autres relations nécessaires
        $stockMovement->load(['utilisateur']);
        
        // Vérification finale des données avant envoi à la vue
        \Illuminate\Support\Facades\Log::info('Produit avant envoi à la vue: ' . ($stockMovement->produit ? 'OUI' : 'NON'));
        if ($stockMovement->produit) {
            \Illuminate\Support\Facades\Log::info('Nom du produit: ' . $stockMovement->produit->nom);
            \Illuminate\Support\Facades\Log::info('Catégorie chargée: ' . ($stockMovement->produit->categorie ? 'OUI' : 'NON'));
            \Illuminate\Support\Facades\Log::info('Unité chargée: ' . ($stockMovement->produit->uniteVente ? 'OUI' : 'NON'));
        }
        
        return view('stock-movements.show', compact('stockMovement'));
    }

    public function destroy(MouvementStock $stockMovement)
    {
        try {
            DB::beginTransaction();

            $product = $stockMovement->produit;
            $movementType = $stockMovement->type;
            $quantity = $stockMovement->quantite_apres_conditionnement;

            switch ($movementType) {
                case 'entree':
                case 'ajustement_positif':
                    if ($product->stock < $quantity) {
                         throw new \Exception('Stock insuffisant pour annuler ce mouvement.');
                    }
                    $product->stock -= $quantity;
                    break;
                case 'sortie':
                case 'ajustement_negatif':
                    $product->stock += $quantity;
                    break;
            }

            $product->save();

            $stockMovement->delete();

            DB::commit();

            return redirect()
                ->route('stock-movements.index')
                ->with('success', 'Mouvement de stock annulé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
             Log::error('Erreur lors de l\'annulation du mouvement de stock : ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Erreur lors de l\'annulation du mouvement : ' . $e->getMessage());
        }
    }

    /**
     * Recréer un mouvement de stock supprimé
     */
    public function recreate($id = 2)
    {
        try {
            DB::beginTransaction();
            
            // Création d'un nouveau mouvement de stock avec les données par défaut
            $mouvement = new MouvementStock();
            $mouvement->produit_id = 8; // ID du produit CAGE
            $mouvement->type = 'entree';
            $mouvement->quantite_avant_conditionnement = 0;
            $mouvement->quantite_apres_conditionnement = 100;
            $mouvement->quantite_avant_unite = 0;
            $mouvement->quantite_apres_unite = 100;
            $mouvement->motif = 'Recréation du mouvement de stock';
            $mouvement->date_mouvement = now();
            $mouvement->save();
            
            DB::commit();
            
            return redirect()
                ->route('mouvements-stock.show', $mouvement->id)
                ->with('success', 'Mouvement de stock recréé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la recréation du mouvement : ' . $e->getMessage());
        }
    }
}