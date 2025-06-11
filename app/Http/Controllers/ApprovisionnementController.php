<?php

namespace App\Http\Controllers;

use App\Models\Approvisionnement;
use App\Models\DetailApprovisionnement;
use App\Models\FactureFournisseur;
use App\Models\PaiementApprovisionnement;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApprovisionnementController extends Controller
{
    public function index(Request $request)
    {
        $query = Approvisionnement::with(['fournisseur', 'user']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }

        if ($request->filled('date_debut')) {
            $query->where('date_commande', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_commande', '<=', $request->date_fin);
        }

        if ($request->filled('recherche')) {
            $query->recherche($request->recherche);
        }

        // Tri
        $sort = $request->get('sort', 'date_commande');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $approvisionnements = $query->paginate(10);

        return view('approvisionnements.index', compact('approvisionnements'));
    }

    public function create()
    {
        $produits = Produit::orderBy('nom')->get();
        return view('approvisionnements.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'required|date|after_or_equal:date_commande',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|numeric|min:0.01',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
            'produits.*.remise' => 'nullable|numeric|min:0|max:100',
            'mode_paiement' => 'nullable|in:especes,cheque,virement,autre',
            'reference_paiement' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Créer l'approvisionnement
            $approvisionnement = Approvisionnement::create([
                'numero' => 'APP-' . strtoupper(Str::random(8)),
                'fournisseur_id' => $validated['fournisseur_id'],
                'user_id' => auth()->check() ? auth()->id() : 1, // User ID 1 par défaut si non connecté
                'date_commande' => $validated['date_commande'],
                'date_livraison_prevue' => $validated['date_livraison_prevue'],
                'statut' => 'en_attente',
                'mode_paiement' => $validated['mode_paiement'],
                'reference_paiement' => $validated['reference_paiement'],
                'notes' => $validated['notes']
            ]);

            // Créer les détails
            foreach ($validated['produits'] as $produit) {
                $detail = $approvisionnement->details()->create([
                    'produit_id' => $produit['produit_id'],
                    'quantite' => $produit['quantite'],
                    'prix_unitaire' => $produit['prix_unitaire'],
                    'remise' => $produit['remise'] ?? 0,
                    'notes' => $produit['notes'] ?? null
                ]);

                $detail->calculerTotalLigne();
            }

            // Calculer les montants totaux
            $approvisionnement->calculerMontants();

            DB::commit();

            return redirect()
                ->route('approvisionnements.show', $approvisionnement)
                ->with('success', 'Approvisionnement créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'approvisionnement : ' . $e->getMessage());
        }
    }

    public function show(Approvisionnement $approvisionnement)
    {
        $approvisionnement->load(['fournisseur', 'user', 'details.produit', 'factures', 'paiements.user']);

        return view('approvisionnements.show', compact('approvisionnement'));
    }

    public function edit(Approvisionnement $approvisionnement)
    {
        if ($approvisionnement->statut !== 'en_attente') {
            return back()->with('error', 'Seuls les approvisionnements en attente peuvent être modifiés.');
        }

        $approvisionnement->load('details.produit');
        $produits = Produit::orderBy('nom')->get();

        return view('approvisionnements.edit', compact('approvisionnement', 'produits'));
    }

    public function update(Request $request, Approvisionnement $approvisionnement)
    {
        if ($approvisionnement->statut !== 'en_attente') {
            return back()->with('error', 'Seuls les approvisionnements en attente peuvent être modifiés.');
        }

        $validated = $request->validate([
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'required|date|after_or_equal:date_commande',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|numeric|min:0.01',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
            'produits.*.remise' => 'nullable|numeric|min:0|max:100',
            'mode_paiement' => 'nullable|in:especes,cheque,virement,autre',
            'reference_paiement' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Mettre à jour l'approvisionnement
            $approvisionnement->update([
                'date_commande' => $validated['date_commande'],
                'date_livraison_prevue' => $validated['date_livraison_prevue'],
                'mode_paiement' => $validated['mode_paiement'],
                'reference_paiement' => $validated['reference_paiement'],
                'notes' => $validated['notes']
            ]);

            // Supprimer les anciens détails
            $approvisionnement->details()->delete();

            // Créer les nouveaux détails
            foreach ($validated['produits'] as $produit) {
                $detail = $approvisionnement->details()->create([
                    'produit_id' => $produit['produit_id'],
                    'quantite' => $produit['quantite'],
                    'prix_unitaire' => $produit['prix_unitaire'],
                    'remise' => $produit['remise'] ?? 0,
                    'notes' => $produit['notes'] ?? null
                ]);

                $detail->calculerTotalLigne();
            }

            // Recalculer les montants totaux
            $approvisionnement->calculerMontants();

            DB::commit();

            return redirect()
                ->route('approvisionnements.show', $approvisionnement)
                ->with('success', 'Approvisionnement mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'approvisionnement : ' . $e->getMessage());
        }
    }

    public function destroy(Approvisionnement $approvisionnement)
    {
        if ($approvisionnement->statut !== 'en_attente') {
            return back()->with('error', 'Seuls les approvisionnements en attente peuvent être supprimés.');
        }

        try {
            DB::beginTransaction();

            // Supprimer les détails
            $approvisionnement->details()->delete();

            // Supprimer l'approvisionnement
            $approvisionnement->delete();

            DB::commit();

            return redirect()
                ->route('approvisionnements.index')
                ->with('success', 'Approvisionnement supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression de l\'approvisionnement : ' . $e->getMessage());
        }
    }

    public function confirmer(Approvisionnement $approvisionnement)
    {
        if ($approvisionnement->statut !== 'en_attente') {
            return back()->with('error', 'Seul un approvisionnement en attente peut être confirmé.');
        }

        try {
            $approvisionnement->update(['statut' => 'confirmee']);

            return redirect()
                ->route('approvisionnements.show', $approvisionnement)
                ->with('success', 'Approvisionnement confirmé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la confirmation de l\'approvisionnement : ' . $e->getMessage());
        }
    }

    public function livrer(Request $request, Approvisionnement $approvisionnement)
    {
        if (!in_array($approvisionnement->statut, ['confirmee', 'en_cours_livraison'])) {
            return back()->with('error', 'Seul un approvisionnement confirmé ou en cours de livraison peut être livré.');
        }

        $validated = $request->validate([
            'produits' => 'required|array',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite_recue' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Mettre à jour les quantités reçues
            foreach ($validated['produits'] as $produit) {
                $detail = $approvisionnement->details()
                    ->where('produit_id', $produit['produit_id'])
                    ->first();

                if ($detail) {
                    $detail->update([
                        'quantite_recue' => $produit['quantite_recue'],
                        'notes' => $produit['notes'] ?? null
                    ]);
                }
            }

            // Marquer comme livré
            $approvisionnement->marquerCommeLivree();

            DB::commit();

            return redirect()
                ->route('approvisionnements.show', $approvisionnement)
                ->with('success', 'Approvisionnement livré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la livraison de l\'approvisionnement : ' . $e->getMessage());
        }
    }

    public function annuler(Approvisionnement $approvisionnement)
    {
        try {
            $approvisionnement->annuler();

            return redirect()
                ->route('approvisionnements.show', $approvisionnement)
                ->with('success', 'Approvisionnement annulé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'annulation de l\'approvisionnement : ' . $e->getMessage());
        }
    }

    public function factures(Approvisionnement $approvisionnement)
    {
        $factures = $approvisionnement->factures()
            ->with('paiements.user')
            ->latest()
            ->paginate(10);

        return view('approvisionnements.factures', compact('approvisionnement', 'factures'));
    }

    public function paiements(Approvisionnement $approvisionnement)
    {
        $paiements = $approvisionnement->paiements()
            ->with(['user', 'facture'])
            ->latest()
            ->paginate(10);

        return view('approvisionnements.paiements', compact('approvisionnement', 'paiements'));
    }
} 