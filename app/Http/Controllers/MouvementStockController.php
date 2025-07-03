<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\Superette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MouvementStockController extends Controller
{
    public function index()
    {
        $activeSuperetteId = activeSuperetteId();
        
        if (!$activeSuperetteId) {
            return redirect()->route('superettes.select')
                ->with('error', 'Vous devez sélectionner une superette active pour voir les mouvements de stock.');
        }
        
        $mouvements = MouvementStock::with(['produit', 'utilisateur'])
            ->where('superette_id', $activeSuperetteId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('stock-movements.index', compact('mouvements'));
    }
    
    public function create()
    {
        $activeSuperetteId = activeSuperetteId();
        
        if (!$activeSuperetteId) {
            return redirect()->route('superettes.select')
                ->with('error', 'Vous devez sélectionner une superette active avant de créer un mouvement de stock.');
        }
        
        // Récupérer uniquement les produits de la superette active
        $produits = Produit::where('superette_id', $activeSuperetteId)
            ->orderBy('nom')
            ->get();
            
        // Pour les transferts, récupérer toutes les autres superettes
        $superettes = Superette::where('id', '!=', $activeSuperetteId)
            ->where('actif', true)
            ->orderBy('nom')
            ->get();
            
        return view('stock-movements.create', compact('produits', 'superettes'));
    }
    
    public function store(Request $request)
    {
        $activeSuperetteId = activeSuperetteId();
        
        if (!$activeSuperetteId) {
            return redirect()->route('superettes.select')
                ->with('error', 'Vous devez sélectionner une superette active avant de créer un mouvement de stock.');
        }
        
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'type' => 'required|in:entree,sortie,ajustement,perte,transfert',
            'quantite' => 'required|numeric|min:0.01',
            'motif' => 'required|string|max:255',
            'date_peremption' => 'nullable|date|after:today',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'superette_destination_id' => 'nullable|required_if:type,transfert|exists:superettes,id|different:active_superette_id',
        ]);

        try {
            DB::beginTransaction();
            
            $produit = Produit::findOrFail($validated['produit_id']);
            
            // Vérifier que le produit appartient à la superette active
            if ($produit->superette_id != $activeSuperetteId) {
                return back()->with('error', 'Ce produit n\'appartient pas à la superette active.');
            }
            
            // Créer le mouvement de stock
            $mouvement = new MouvementStock();
            $mouvement->produit_id = $validated['produit_id'];
            $mouvement->type = $validated['type'];
            $mouvement->quantite = $validated['quantite'];
            $mouvement->motif = $validated['motif'];
            $mouvement->date_peremption = $validated['date_peremption'] ?? null;
            $mouvement->prix_unitaire = $validated['prix_unitaire'] ?? $produit->prix_achat_ht;
            $mouvement->user_id = auth()->id();
            $mouvement->superette_id = $activeSuperetteId;
            
            // Si c'est un transfert, enregistrer la superette de destination
            if ($validated['type'] === 'transfert' && isset($validated['superette_destination_id'])) {
                $mouvement->superette_destination_id = $validated['superette_destination_id'];
            }
            
            $mouvement->save();
            
            // Mettre à jour le stock du produit
            $this->updateProductStock($produit, $validated['type'], $validated['quantite']);
            
            DB::commit();
            
            return redirect()->route('mouvements-stock.index')
                ->with('success', 'Mouvement de stock enregistré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'enregistrement du mouvement : ' . $e->getMessage());
        }
    }
    
    /**
     * Met à jour le stock d'un produit en fonction du type de mouvement
     *
     * @param Produit $produit
     * @param string $type
     * @param float $quantite
     * @return void
     */
    protected function updateProductStock(Produit $produit, string $type, float $quantite)
    {
        switch ($type) {
            case 'entree':
                $produit->stock += $quantite;
                break;
            case 'sortie':
            case 'perte':
            case 'transfert':
                if ($produit->stock < $quantite) {
                    throw new \Exception('Stock insuffisant pour ce mouvement.');
                }
                $produit->stock -= $quantite;
                break;
            case 'ajustement':
                $produit->stock = $quantite;
                break;
        }
        
        $produit->save();
    }
    
    public function show(MouvementStock $mouvementStock)
    {
        $activeSuperetteId = activeSuperetteId();
        
        if ($mouvementStock->superette_id != $activeSuperetteId) {
            return redirect()->route('mouvements-stock.index')
                ->with('error', 'Ce mouvement de stock n\'appartient pas à la superette active.');
        }
        
        return view('stock-movements.show', compact('mouvementStock'));
    }
} 