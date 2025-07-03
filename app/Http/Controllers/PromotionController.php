<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Produit;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    /**
     * Récupère les détails d'un produit pour l'affichage AJAX
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
            
            // Vérifier si le produit a déjà une promotion active
            $promotionsActives = Promotion::where('produit_id', $id)
                ->where('actif', true)
                ->where('date_debut', '<=', now())
                ->where('date_fin', '>=', now())
                ->get();
                
            // Prix actuel et prix après réduction si une promotion est active
            $prixActuel = $produit->prix_vente_ttc;
            $prixPromotionnel = null;
            $promotion = null;
            
            if ($promotionsActives->count() > 0) {
                $promotion = $promotionsActives->first();
                $prixPromotionnel = $promotion->calculerPrixReduit($prixActuel);
            }
            
            // Construction du résumé du produit
            $productSummary = [
                'nom' => $produit->nom,
                'categorie' => $produit->categorie ? $produit->categorie->nom : 'Non catégorisé',
                'stock' => $produit->stock,
                'unite' => $produit->uniteVente ? $produit->uniteVente->symbole : '',
                'prix_vente' => $produit->prix_vente_ht,
                'prix_vente_ttc' => $prixActuel,
                'en_promotion' => $promotionsActives->count() > 0,
                'promotion_active' => $promotion ? [
                    'type' => $promotion->type,
                    'valeur' => $promotion->valeur,
                    'prix_promo' => $prixPromotionnel,
                    'date_fin' => $promotion->date_fin->format('d/m/Y H:i'),
                    'description' => $promotion->description
                ] : null
            ];
            
            return response()->json([
                'success' => true,
                'produit' => $productSummary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = Promotion::with('produit')
            ->latest()
            ->paginate(10);

        // Calculer les statistiques en utilisant des requêtes distinctes pour les totaux corrects
        $totalPromotionsActives = Promotion::active()->count();
        $totalProduitsEnPromotion = Promotion::active()->distinct('produit_id')->count();
        $totalPromotionsAVenir = Promotion::where('date_debut', '>', now())->where('actif', true)->count(); // Considérer seulement les promotions à venir actives

        return view('promotions.index', compact('promotions', 'totalPromotionsActives', 'totalProduitsEnPromotion', 'totalPromotionsAVenir'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produits = Produit::where('actif', true)->get();
        return view('promotions.create', compact('produits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'type' => 'required|in:pourcentage,montant',
            'valeur' => 'required|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'description' => 'nullable|string|max:500',
            'actif' => 'boolean'
        ]);

        // Ajouter automatiquement la superette active
        // Utiliser active_superette_id qui est défini par le middleware CheckSuperetteSelected
        $validated['superette_id'] = session('active_superette_id');
        
        // Si pour une raison quelconque la superette_id n'est pas dans la session, utiliser celle de l'utilisateur
        if (!$validated['superette_id'] && auth()->check() && auth()->user()->superette_id) {
            $validated['superette_id'] = auth()->user()->superette_id;
        }
        
        // Si toujours pas de superette_id, utiliser la superette par défaut (ID 1)
        if (!$validated['superette_id']) {
            $validated['superette_id'] = 1; // Superette par défaut
        }

        $promotion = Promotion::create($validated);

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promotion créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        $produits = Produit::where('actif', true)->get();
        return view('promotions.edit', compact('promotion', 'produits'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'type' => 'required|in:pourcentage,montant',
            'valeur' => 'required|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'description' => 'nullable|string|max:500',
            'actif' => 'boolean'
        ]);

        // S'assurer que la superette_id reste celle de la promotion existante
        // ou utiliser celle de la session si la promotion n'en a pas
        if (!$promotion->superette_id) {
            // Utiliser active_superette_id qui est défini par le middleware CheckSuperetteSelected
            $validated['superette_id'] = session('active_superette_id');
            
            // Si pour une raison quelconque la superette_id n'est pas dans la session, utiliser celle de l'utilisateur
            if (!$validated['superette_id'] && auth()->check() && auth()->user()->superette_id) {
                $validated['superette_id'] = auth()->user()->superette_id;
            }
            
            // Si toujours pas de superette_id, utiliser la superette par défaut (ID 1)
            if (!$validated['superette_id']) {
                $validated['superette_id'] = 1; // Superette par défaut
            }
        }

        $promotion->update($validated);

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promotion mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promotion supprimée avec succès.');
    }
}
