<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\RetourVente;
use App\Models\DetailRetour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class RetourVenteController extends Controller
{
    public function __construct()
    {
    
        // Note: Les middlewares ont été déplacés dans routes/web.php pour compatibilité avec Laravel 12
    }

    /**
     * Affiche la liste des retours
     */
    public function index(Request $request)
    {
        $query = RetourVente::with(['vente.client', 'employe', 'details.produit'])
            ->orderBy('date_retour', 'desc');

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_retour', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_retour', '<=', $request->date_fin);
        }

        if ($request->filled('employe_id')) {
            $query->where('employe_id', $request->employe_id);
        }

        if ($request->filled('vente_id')) {
            $query->where('vente_id', $request->vente_id);
        }

        $retours = $query->paginate(10);

        return view('retours.index', compact('retours'));
    }

    /**
     * Affiche le formulaire de création d'un retour
     */
    public function create(Vente $vente)
    {
        if (!$vente->peutEtreRetournee()) {
            return redirect()->route('ventes.show', $vente)
                ->with('error', 'Cette vente ne peut pas être retournée.');
        }

        $vente->load(['details.produit']);
        return view('retours.create', compact('vente'));
    }

    /**
     * Enregistre un nouveau retour
     */
    public function store(Request $request, Vente $vente)
    {
        if (!$vente->peutEtreRetournee()) {
            return redirect()->route('ventes.show', $vente)
                ->with('error', 'Cette vente ne peut pas être retournée.');
        }

        $request->validate([
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite_retournee' => 'required|integer|min:1',
            'produits.*.raison_retour' => 'required|string|max:255',
            'motif_retour' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Créer le retour
            $retour = new RetourVente([
                'employe_id' => null,
                'motif_retour' => $request->motif_retour,
                'notes' => $request->notes,
                'statut' => RetourVente::STATUT_EN_COURS
            ]);

            $vente->retours()->save($retour);

            // Ajouter les détails du retour
            foreach ($request->produits as $produit) {
                $detailVente = $vente->details()
                    ->where('produit_id', $produit['produit_id'])
                    ->first();

                if (!$detailVente) {
                    throw new \Exception("Le produit n'a pas été vendu dans cette vente.");
                }

                if ($produit['quantite_retournee'] > $detailVente->quantite) {
                    throw new \Exception("La quantité retournée ne peut pas être supérieure à la quantité vendue.");
                }

                $detail = new DetailRetour([
                    'produit_id' => $produit['produit_id'],
                    'quantite_retournee' => $produit['quantite_retournee'],
                    'prix_unitaire' => $detailVente->prix_unitaire,
                    'raison_retour' => $produit['raison_retour']
                ]);

                $retour->details()->save($detail);
            }

            DB::commit();
            return redirect()->route('retours.show', $retour)
                ->with('success', 'Retour enregistré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement du retour : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un retour
     */
    public function show(RetourVente $retour)
    {
        $retour->load(['vente.client', 'employe', 'details.produit']);
        return view('retours.show', compact('retour'));
    }

    /**
     * Termine un retour
     */
    public function terminer(RetourVente $retour)
    {
        try {
            DB::beginTransaction();

            $retour->terminer();

            DB::commit();
            return redirect()->route('retours.show', $retour)
                ->with('success', 'Retour terminé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la finalisation du retour : ' . $e->getMessage());
        }
    }

    /**
     * Annule un retour
     */
    public function annuler(RetourVente $retour)
    {
        try {
            DB::beginTransaction();

            $retour->annuler();

            DB::commit();
            return redirect()->route('retours.show', $retour)
                ->with('success', 'Retour annulé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'annulation du retour : ' . $e->getMessage());
        }
    }

    /**
     * Supprime un retour
     */
    public function destroy(RetourVente $retour)
    {
        if ($retour->statut !== RetourVente::STATUT_EN_COURS) {
            return back()->with('error', 'Seuls les retours en cours peuvent être supprimés.');
        }

        try {
            DB::beginTransaction();

            $vente = $retour->vente;
            $retour->delete();

            DB::commit();
            return redirect()->route('ventes.show', $vente)
                ->with('success', 'Retour supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression du retour : ' . $e->getMessage());
        }
    }
} 