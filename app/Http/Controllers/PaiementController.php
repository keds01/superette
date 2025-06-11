<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaiementController extends Controller
{
    public function __construct()
    {
    
        // Note: Les middlewares ont été déplacés dans routes/web.php pour compatibilité avec Laravel 12
    }

    /**
     * Affiche la liste des paiements
     */
    public function index(Request $request)
    {
        $query = Paiement::with(['vente.client', 'vente.employe'])
            ->orderBy('date_paiement', 'desc');

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('mode_paiement')) {
            $query->where('mode_paiement', $request->mode_paiement);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_paiement', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_paiement', '<=', $request->date_fin);
        }

        if ($request->filled('vente_id')) {
            $query->where('vente_id', $request->vente_id);
        }

        $paiements = $query->paginate(10);

        return view('paiements.index', compact('paiements'));
    }

    /**
     * Enregistre un nouveau paiement
     */
    public function store(Request $request, Vente $vente)
    {
        $request->validate([
            'mode_paiement' => 'required|in:' . implode(',', [
                Paiement::MODE_ESPECES,
                Paiement::MODE_CARTE,
                Paiement::MODE_CHEQUE,
                Paiement::MODE_MOBILE,
                Paiement::MODE_VIREMENT
            ]),
            'montant' => 'required|numeric|min:0.01|max:' . $vente->montant_restant,
            'reference_paiement' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $paiement = new Paiement([
                'mode_paiement' => $request->mode_paiement,
                'montant' => $request->montant,
                'reference_paiement' => $request->reference_paiement,
                'notes' => $request->notes,
                'statut' => Paiement::STATUT_EN_ATTENTE
            ]);

            $vente->paiements()->save($paiement);

            // Si le paiement est en espèces, le valider automatiquement
            if ($request->mode_paiement === Paiement::MODE_ESPECES) {
                $paiement->valider();
            }

            DB::commit();
            return redirect()->route('ventes.show', $vente)
                ->with('success', 'Paiement enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Valide un paiement
     */
    public function valider(Paiement $paiement)
    {
        try {
            DB::beginTransaction();

            $paiement->valider();

            DB::commit();
            return redirect()->route('ventes.show', $paiement->vente)
                ->with('success', 'Paiement validé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la validation du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Refuse un paiement
     */
    public function refuser(Paiement $paiement)
    {
        try {
            DB::beginTransaction();

            $paiement->refuser();

            DB::commit();
            return redirect()->route('ventes.show', $paiement->vente)
                ->with('success', 'Paiement refusé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du refus du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Rembourse un paiement
     */
    public function rembourser(Paiement $paiement)
    {
        try {
            DB::beginTransaction();

            $paiement->rembourser();

            DB::commit();
            return redirect()->route('ventes.show', $paiement->vente)
                ->with('success', 'Paiement remboursé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du remboursement du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Supprime un paiement
     */
    public function destroy(Paiement $paiement)
    {
        if ($paiement->statut !== Paiement::STATUT_EN_ATTENTE) {
            return back()->with('error', 'Seuls les paiements en attente peuvent être supprimés.');
        }

        try {
            DB::beginTransaction();

            $vente = $paiement->vente;
            $paiement->delete();

            DB::commit();
            return redirect()->route('ventes.show', $vente)
                ->with('success', 'Paiement supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression du paiement : ' . $e->getMessage());
        }
    }
} 