<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function index()
    {
        $commandes = Commande::with(['fournisseur'])
            ->latest()
            ->paginate(10);

        return view('commandes.index', compact('commandes'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::where('actif', true)
            ->orderBy('nom')
            ->get();

        $produits = Produit::with(['categorie', 'uniteVente'])
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('commandes.create', compact('fournisseurs', 'produits'));
    }

    public function store(Request $request)
    {
        // Validation améliorée pour vérifier les doublons de produits
        $validated = $request->validate([
            'numero_commande' => 'required|unique:commandes',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'required|date|after_or_equal:date_commande',
            'produits' => 'required|array|min:1',
            'produits.*' => [
                'required',
                'exists:produits,id',
                function ($attribute, $value, $fail) use ($request) {
                    // Vérifier s'il y a des doublons de produits
                    $produits = $request->input('produits');
                    if (count(array_keys($produits, $value)) > 1) {
                        $fail('Le produit a été sélectionné plusieurs fois. Veuillez ajuster les quantités plutôt que de dupliquer le produit.');
                    }
                }
            ],
            'quantites' => 'required|array|min:1',
            'quantites.*' => 'required|numeric|min:0.01',
            'prix_unitaire' => 'required|array|min:1',
            'prix_unitaire.*' => 'required|numeric|min:0.01',
            'montant_total' => 'required|numeric|min:0',
            'devise' => 'required|in:FCFA,EUR,USD',
        ]);

        // Utilisation d'une transaction explicite
        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            $commande = Commande::create([
                'numero_commande' => $validated['numero_commande'],
                'fournisseur_id' => $validated['fournisseur_id'],
                'date_commande' => $validated['date_commande'],
                'date_livraison_prevue' => $validated['date_livraison_prevue'],
                'montant_total' => $validated['montant_total'],
                'devise' => $validated['devise'],
                'statut' => 'en_attente',
            ]);

            // Créer les détails de la commande
            foreach ($validated['produits'] as $index => $produitId) {
                $commande->details()->create([
                    'produit_id' => $produitId,
                    'quantite' => $validated['quantites'][$index],
                    'prix_unitaire' => $validated['prix_unitaire'][$index],
                    'montant_total' => $validated['quantites'][$index] * $validated['prix_unitaire'][$index],
                ]);
            }

            return redirect()
                ->route('commandes.show', $commande)
                ->with('success', 'La commande a été créée avec succès.');
        });
    }

    public function show(Commande $commande)
    {
        $commande->load(['fournisseur', 'details.produit.categorie', 'details.produit.uniteVente']);
        return view('commandes.show', compact('commande'));
    }

    public function edit(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return redirect()
                ->route('commandes.show', $commande)
                ->with('error', 'Seules les commandes en attente peuvent être modifiées.');
        }

        $fournisseurs = Fournisseur::where('actif', true)
            ->orderBy('nom')
            ->get();

        $produits = Produit::with(['categorie', 'uniteVente'])
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        $commande->load(['details']);

        return view('commandes.edit', compact('commande', 'fournisseurs', 'produits'));
    }

    public function update(Request $request, Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return redirect()
                ->route('commandes.show', $commande)
                ->with('error', 'Seules les commandes en attente peuvent être modifiées.');
        }

        // Validation améliorée pour vérifier les doublons de produits
        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'required|date|after_or_equal:date_commande',
            'produits' => 'required|array|min:1',
            'produits.*' => [
                'required',
                'exists:produits,id',
                function ($attribute, $value, $fail) use ($request) {
                    // Vérifier s'il y a des doublons de produits
                    $produits = $request->input('produits');
                    if (count(array_keys($produits, $value)) > 1) {
                        $fail('Le produit a été sélectionné plusieurs fois. Veuillez ajuster les quantités plutôt que de dupliquer le produit.');
                    }
                }
            ],
            'quantites' => 'required|array|min:1',
            'quantites.*' => 'required|numeric|min:0.01',
            'prix_unitaire' => 'required|array|min:1',
            'prix_unitaire.*' => 'required|numeric|min:0.01',
            'montant_total' => 'required|numeric|min:0',
            'devise' => 'required|in:FCFA,EUR,USD',
        ]);

        // Utilisation d'une transaction explicite
        return \Illuminate\Support\Facades\DB::transaction(function () use ($commande, $validated) {
            $commande->update([
                'fournisseur_id' => $validated['fournisseur_id'],
                'date_commande' => $validated['date_commande'],
                'date_livraison_prevue' => $validated['date_livraison_prevue'],
                'montant_total' => $validated['montant_total'],
                'devise' => $validated['devise'],
            ]);

            // Supprimer les anciens détails
            $commande->details()->delete();

            // Créer les nouveaux détails
            foreach ($validated['produits'] as $index => $produitId) {
                $commande->details()->create([
                    'produit_id' => $produitId,
                    'quantite' => $validated['quantites'][$index],
                    'prix_unitaire' => $validated['prix_unitaire'][$index],
                    'montant_total' => $validated['quantites'][$index] * $validated['prix_unitaire'][$index],
                ]);
            }

            return redirect()
                ->route('commandes.show', $commande)
                ->with('success', 'La commande a été mise à jour avec succès.');
        });
    }

    public function destroy(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return redirect()
                ->route('commandes.show', $commande)
                ->with('error', 'Seules les commandes en attente peuvent être supprimées.');
        }

        $commande->details()->delete();
        $commande->delete();

        return redirect()
            ->route('commandes.index')
            ->with('success', 'La commande a été supprimée avec succès.');
    }
}
