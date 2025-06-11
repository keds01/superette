<?php

namespace App\Http\Controllers;

use App\Models\FactureFournisseur;
use App\Models\PaiementApprovisionnement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaiementApprovisionnementController extends Controller
{
    public function index(Request $request)
    {
        $query = PaiementApprovisionnement::with(['facture.fournisseur', 'user']);

        // Filtres
        if ($request->filled('facture_id')) {
            $query->where('facture_id', $request->facture_id);
        }

        if ($request->filled('fournisseur_id')) {
            $query->whereHas('facture', function ($q) use ($request) {
                $q->where('fournisseur_id', $request->fournisseur_id);
            });
        }

        if ($request->filled('date_debut')) {
            $query->where('date_paiement', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_paiement', '<=', $request->date_fin);
        }

        if ($request->filled('mode_paiement')) {
            $query->where('mode_paiement', $request->mode_paiement);
        }

        // Tri
        $sort = $request->get('sort', 'date_paiement');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $paiements = $query->paginate(10);

        return view('paiements-approvisionnements.index', compact('paiements'));
    }

    public function create(FactureFournisseur $facture)
    {
        if ($facture->statut === 'payee') {
            return back()->with('error', 'Cette facture est déjà entièrement payée.');
        }

        if ($facture->statut === 'annulee') {
            return back()->with('error', 'Impossible de payer une facture annulée.');
        }

        return view('paiements-approvisionnements.create', compact('facture'));
    }

    public function store(Request $request, FactureFournisseur $facture)
    {
        if ($facture->statut === 'payee') {
            return back()->with('error', 'Cette facture est déjà entièrement payée.');
        }

        if ($facture->statut === 'annulee') {
            return back()->with('error', 'Impossible de payer une facture annulée.');
        }

        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01|max:' . $facture->montant_restant,
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|in:especes,cheque,virement,autre',
            'reference_paiement' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Créer le paiement
            $paiement = $facture->paiements()->create([
                'user_id' => auth()->check() ? auth()->id() : 1, // User ID 1 par défaut si non connecté
                'montant' => $validated['montant'],
                'date_paiement' => $validated['date_paiement'],
                'mode_paiement' => $validated['mode_paiement'],
                'reference_paiement' => $validated['reference_paiement'],
                'notes' => $validated['notes']
            ]);

            // Mettre à jour la facture
            $facture->increment('montant_paye', $validated['montant']);
            $facture->decrement('montant_restant', $validated['montant']);

            // Mettre à jour le solde du fournisseur
            $facture->fournisseur->decrement('solde_actuel', $validated['montant']);

            // Vérifier si la facture est entièrement payée
            if ($facture->montant_restant <= 0) {
                $facture->update(['statut' => 'payee']);
            }

            DB::commit();

            return redirect()
                ->route('factures-fournisseurs.show', $facture)
                ->with('success', 'Paiement enregistré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement du paiement : ' . $e->getMessage());
        }
    }

    public function show(PaiementApprovisionnement $paiement)
    {
        $paiement->load(['facture.fournisseur', 'user']);
        return view('paiements-approvisionnements.show', compact('paiement'));
    }

    public function edit(PaiementApprovisionnement $paiement)
    {
        if ($paiement->facture->statut === 'payee') {
            return back()->with('error', 'Impossible de modifier un paiement d\'une facture payée.');
        }

        return view('paiements-approvisionnements.edit', compact('paiement'));
    }

    public function update(Request $request, PaiementApprovisionnement $paiement)
    {
        if ($paiement->facture->statut === 'payee') {
            return back()->with('error', 'Impossible de modifier un paiement d\'une facture payée.');
        }

        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01|max:' . ($paiement->facture->montant_restant + $paiement->montant),
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|in:especes,cheque,virement,autre',
            'reference_paiement' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculer la différence de montant
            $difference = $validated['montant'] - $paiement->montant;

            // Mettre à jour le paiement
            $paiement->update([
                'montant' => $validated['montant'],
                'date_paiement' => $validated['date_paiement'],
                'mode_paiement' => $validated['mode_paiement'],
                'reference_paiement' => $validated['reference_paiement'],
                'notes' => $validated['notes']
            ]);

            // Mettre à jour la facture
            $facture = $paiement->facture;
            $facture->increment('montant_paye', $difference);
            $facture->decrement('montant_restant', $difference);

            // Mettre à jour le solde du fournisseur
            $facture->fournisseur->decrement('solde_actuel', $difference);

            // Vérifier si la facture est entièrement payée
            if ($facture->montant_restant <= 0) {
                $facture->update(['statut' => 'payee']);
            } else {
                $facture->update(['statut' => 'validee']);
            }

            DB::commit();

            return redirect()
                ->route('factures-fournisseurs.show', $facture)
                ->with('success', 'Paiement mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour du paiement : ' . $e->getMessage());
        }
    }

    public function destroy(PaiementApprovisionnement $paiement)
    {
        if ($paiement->facture->statut === 'payee') {
            return back()->with('error', 'Impossible de supprimer un paiement d\'une facture payée.');
        }

        try {
            DB::beginTransaction();

            $facture = $paiement->facture;

            // Mettre à jour la facture
            $facture->decrement('montant_paye', $paiement->montant);
            $facture->increment('montant_restant', $paiement->montant);

            // Mettre à jour le solde du fournisseur
            $facture->fournisseur->increment('solde_actuel', $paiement->montant);

            // Mettre à jour le statut de la facture
            $facture->update(['statut' => 'validee']);

            // Supprimer le paiement
            $paiement->delete();

            DB::commit();

            return redirect()
                ->route('factures-fournisseurs.show', $facture)
                ->with('success', 'Paiement supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression du paiement : ' . $e->getMessage());
        }
    }
} 