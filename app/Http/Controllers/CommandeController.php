<?php

namespace App\Http\Controllers;

use App\Models\Approvisionnement;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    /**
     * Affiche la liste des commandes
     */
    public function index()
    {
        $commandes = Approvisionnement::with(['fournisseur', 'details.produit'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('commandes.index', compact('commandes'));
    }

    /**
     * Affiche le formulaire de création d'une commande
     */
    public function create()
    {
        $fournisseurs = Fournisseur::where('actif', true)->get();
        $produits = Produit::orderBy('nom')->get();

        return view('commandes.create', compact('fournisseurs', 'produits'));
    }

    /**
     * Enregistre une nouvelle commande
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|numeric|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $commande = Approvisionnement::create([
                'fournisseur_id' => $validated['fournisseur_id'],
                'date_commande' => $validated['date_commande'],
                'date_livraison_prevue' => $validated['date_livraison_prevue'],
                'statut' => 'en_attente',
                'notes' => $validated['notes'] ?? null,
                'user_id' => Auth::id()
            ]);

            // Créer les détails de la commande
            foreach ($validated['produits'] as $produit) {
                $commande->details()->create([
                    'produit_id' => $produit['produit_id'],
                    'quantite' => $produit['quantite'],
                    'prix_unitaire' => $produit['prix_unitaire'],
                    'sous_total' => $produit['quantite'] * $produit['prix_unitaire']
                ]);
            }

            // Calculer le montant total
            $total = $commande->details->sum('sous_total');
            $commande->update(['montant_total' => $total]);

            DB::commit();

            return redirect()->route('commandes.show', $commande)
                ->with('success', 'Commande créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la création de la commande: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Approvisionnement $commande)
    {
        $commande->load(['fournisseur', 'details.produit', 'user']);
        
        return view('commandes.show', compact('commande'));
    }

    /**
     * Affiche le formulaire de modification d'une commande
     */
    public function edit(Approvisionnement $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->with('error', 'Impossible de modifier une commande qui n\'est pas en attente.');
        }

        $fournisseurs = Fournisseur::where('actif', true)->get();
        $produits = Produit::orderBy('nom')->get();
        $commande->load('details.produit');

        return view('commandes.edit', compact('commande', 'fournisseurs', 'produits'));
    }

    /**
     * Met à jour une commande
     */
    public function update(Request $request, Approvisionnement $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->with('error', 'Impossible de modifier une commande qui n\'est pas en attente.');
        }

        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|numeric|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Mettre à jour la commande
            $commande->update([
                'fournisseur_id' => $validated['fournisseur_id'],
                'date_commande' => $validated['date_commande'],
                'date_livraison_prevue' => $validated['date_livraison_prevue'],
                'notes' => $validated['notes'] ?? null
            ]);

            // Supprimer les anciens détails
            $commande->details()->delete();

            // Créer les nouveaux détails
            foreach ($validated['produits'] as $produit) {
                $commande->details()->create([
                    'produit_id' => $produit['produit_id'],
                    'quantite' => $produit['quantite'],
                    'prix_unitaire' => $produit['prix_unitaire'],
                    'sous_total' => $produit['quantite'] * $produit['prix_unitaire']
                ]);
            }

            // Recalculer le montant total
            $total = $commande->details->sum('sous_total');
            $commande->update(['montant_total' => $total]);

            DB::commit();

            return redirect()->route('commandes.show', $commande)
                ->with('success', 'Commande mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la commande: ' . $e->getMessage());
        }
    }

    /**
     * Supprime une commande
     */
    public function destroy(Approvisionnement $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return back()->with('error', 'Impossible de supprimer une commande qui n\'est pas en attente.');
        }

        try {
            DB::beginTransaction();
            
            // Supprimer les détails
            $commande->details()->delete();
            
            // Supprimer la commande
            $commande->delete();

            DB::commit();

            return redirect()->route('commandes.index')
                ->with('success', 'Commande supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression de la commande: ' . $e->getMessage());
        }
    }
} 