<?php

namespace App\Http\Controllers;

use App\Models\Approvisionnement;
use App\Models\FactureFournisseur;
use App\Models\PaiementApprovisionnement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FactureFournisseurController extends Controller
{
    public function index(Request $request)
    {
        $query = FactureFournisseur::with(['fournisseur', 'approvisionnement']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }

        if ($request->filled('date_debut')) {
            $query->where('date_facture', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_facture', '<=', $request->date_fin);
        }

        if ($request->filled('recherche')) {
            $query->recherche($request->recherche);
        }

        // Tri
        $sort = $request->get('sort', 'date_facture');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $factures = $query->paginate(10);

        return view('factures-fournisseurs.index', compact('factures'));
    }

    public function create(Approvisionnement $approvisionnement)
    {
        if ($approvisionnement->statut !== 'livree') {
            return back()->with('error', 'Seuls les approvisionnements livrés peuvent avoir une facture.');
        }

        if ($approvisionnement->factures()->exists()) {
            return back()->with('error', 'Cet approvisionnement a déjà une facture.');
        }

        return view('factures-fournisseurs.create', compact('approvisionnement'));
    }

    public function store(Request $request, Approvisionnement $approvisionnement)
    {
        if ($approvisionnement->statut !== 'livree') {
            return back()->with('error', 'Seuls les approvisionnements livrés peuvent avoir une facture.');
        }

        $validated = $request->validate([
            'numero_facture' => 'required|string|max:255|unique:factures_fournisseurs',
            'date_facture' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_facture',
            'montant_ht' => 'required|numeric|min:0',
            'taux_tva' => 'required|numeric|min:0|max:100',
            'montant_tva' => 'required|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:especes,cheque,virement,autre',
            'reference_paiement' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Créer la facture
            $facture = $approvisionnement->factures()->create([
                'numero' => $validated['numero_facture'],
                'fournisseur_id' => $approvisionnement->fournisseur_id,
                'date_facture' => $validated['date_facture'],
                'date_echeance' => $validated['date_echeance'],
                'montant_ht' => $validated['montant_ht'],
                'taux_tva' => $validated['taux_tva'],
                'montant_tva' => $validated['montant_tva'],
                'montant_ttc' => $validated['montant_ttc'],
                'montant_paye' => 0,
                'montant_restant' => $validated['montant_ttc'],
                'statut' => 'en_attente',
                'mode_paiement' => $validated['mode_paiement'],
                'reference_paiement' => $validated['reference_paiement'],
                'notes' => $validated['notes']
            ]);

            // Mettre à jour le solde du fournisseur
            $approvisionnement->fournisseur->increment('solde_actuel', $validated['montant_ttc']);

            DB::commit();

            return redirect()
                ->route('factures-fournisseurs.show', $facture)
                ->with('success', 'Facture créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la facture : ' . $e->getMessage());
        }
    }

    public function show(FactureFournisseur $facture)
    {
        $facture->load(['fournisseur', 'approvisionnement.details.produit', 'paiements.user']);

        return view('factures-fournisseurs.show', compact('facture'));
    }

    public function edit(FactureFournisseur $facture)
    {
        if ($facture->statut !== 'en_attente') {
            return back()->with('error', 'Seules les factures en attente peuvent être modifiées.');
        }

        return view('factures-fournisseurs.edit', compact('facture'));
    }

    public function update(Request $request, FactureFournisseur $facture)
    {
        if ($facture->statut !== 'en_attente') {
            return back()->with('error', 'Seules les factures en attente peuvent être modifiées.');
        }

        $validated = $request->validate([
            'numero_facture' => 'required|string|max:255|unique:factures_fournisseurs,numero,' . $facture->id,
            'date_facture' => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_facture',
            'montant_ht' => 'required|numeric|min:0',
            'taux_tva' => 'required|numeric|min:0|max:100',
            'montant_tva' => 'required|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:especes,cheque,virement,autre',
            'reference_paiement' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculer la différence de montant
            $difference = $validated['montant_ttc'] - $facture->montant_ttc;

            // Mettre à jour la facture
            $facture->update([
                'numero' => $validated['numero_facture'],
                'date_facture' => $validated['date_facture'],
                'date_echeance' => $validated['date_echeance'],
                'montant_ht' => $validated['montant_ht'],
                'taux_tva' => $validated['taux_tva'],
                'montant_tva' => $validated['montant_tva'],
                'montant_ttc' => $validated['montant_ttc'],
                'montant_restant' => $facture->montant_restant + $difference,
                'mode_paiement' => $validated['mode_paiement'],
                'reference_paiement' => $validated['reference_paiement'],
                'notes' => $validated['notes']
            ]);

            // Mettre à jour le solde du fournisseur
            if ($difference != 0) {
                $facture->fournisseur->increment('solde_actuel', $difference);
            }

            DB::commit();

            return redirect()
                ->route('factures-fournisseurs.show', $facture)
                ->with('success', 'Facture mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la facture : ' . $e->getMessage());
        }
    }

    public function destroy(FactureFournisseur $facture)
    {
        if ($facture->statut !== 'en_attente') {
            return back()->with('error', 'Seules les factures en attente peuvent être supprimées.');
        }

        try {
            DB::beginTransaction();

            // Mettre à jour le solde du fournisseur
            $facture->fournisseur->decrement('solde_actuel', $facture->montant_ttc);

            // Supprimer la facture
            $facture->delete();

            DB::commit();

            return redirect()
                ->route('factures-fournisseurs.index')
                ->with('success', 'Facture supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression de la facture : ' . $e->getMessage());
        }
    }

    public function valider(FactureFournisseur $facture)
    {
        if ($facture->statut !== 'en_attente') {
            return back()->with('error', 'Seules les factures en attente peuvent être validées.');
        }

        try {
            $facture->update(['statut' => 'validee']);

            return redirect()
                ->route('factures-fournisseurs.show', $facture)
                ->with('success', 'Facture validée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la validation de la facture : ' . $e->getMessage());
        }
    }

    public function annuler(FactureFournisseur $facture)
    {
        if ($facture->statut === 'payee') {
            return back()->with('error', 'Impossible d\'annuler une facture déjà payée.');
        }

        try {
            DB::beginTransaction();

            // Mettre à jour le solde du fournisseur
            $facture->fournisseur->decrement('solde_actuel', $facture->montant_restant);

            // Annuler la facture
            $facture->update([
                'statut' => 'annulee',
                'montant_restant' => 0
            ]);

            DB::commit();

            return redirect()
                ->route('factures-fournisseurs.show', $facture)
                ->with('success', 'Facture annulée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'annulation de la facture : ' . $e->getMessage());
        }
    }

    public function paiements(FactureFournisseur $facture)
    {
        $paiements = $facture->paiements()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('factures-fournisseurs.paiements', compact('facture', 'paiements'));
    }
} 