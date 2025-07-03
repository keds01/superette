<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\Reception;
use App\Models\DetailReception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReceptionController extends Controller
{
    public function index()
    {
        $receptions = Reception::with(['fournisseur', 'user'])
            ->latest()
            ->paginate(15);

        return view('receptions.index', compact('receptions'));
    }

    public function create(Request $request)
    {
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $produits = Produit::orderBy('nom')->get();

        // Chargement strict de la commande si commande_id fourni et valide
        $commande = null;
        if ($request->filled('commande_id')) {
            $commande = \App\Models\Commande::with(['fournisseur', 'details.produit'])->find($request->commande_id);
            if (!$commande) {
                // commande_id invalide : on reste en mode formulaire libre
                $commande = null;
            }
        }

        return view('receptions.create', compact('fournisseurs', 'produits', 'commande'));
    }

    public function store(Request $request)
    {
        // Force la sauvegarde sans validation si demandé
        if ($request->has('direct_save')) {
            try {
                DB::beginTransaction();
                
                // Création de la réception
                $reception = new Reception();
                $reception->numero = 'REC-' . strtoupper(Str::random(8));
                $reception->fournisseur_id = $request->input('fournisseur_id');
                $reception->user_id = auth()->check() ? auth()->id() : 1; // Utilisateur connecté sinon ID système
                $reception->date_reception = $request->input('date_reception');
                $reception->numero_facture = $request->input('numero_facture');
                $reception->mode_paiement = $request->input('mode_paiement');
                $reception->description = $request->input('description');
                $reception->statut = 'en_cours';
                $reception->save();
                
                // Création des détails
                $produits = $request->input('produits', []);
                foreach ($produits as $produitData) {
                    if (isset($produitData['produit_id']) && isset($produitData['quantite']) && isset($produitData['prix_unitaire'])) {
                        $detailReception = new DetailReception();
                        $detailReception->reception_id = $reception->id;
                        $detailReception->produit_id = $produitData['produit_id'];
                        $detailReception->quantite = $produitData['quantite'];
                        $detailReception->prix_unitaire = $produitData['prix_unitaire'];
                        $detailReception->date_peremption = $produitData['date_peremption'] ?? null;
                        $detailReception->save();
                        
                        // Mise à jour du stock
                        $produit = Produit::find($produitData['produit_id']);
                        if ($produit) {
                            $produit->increment('stock', $produitData['quantite']);
                        }
                    }
                }
                
                DB::commit();
                return redirect()->route('receptions.show', $reception)
                    ->with('success', 'Réception enregistrée avec succès (mode direct).');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Erreur sauvegarde directe: ' . $e->getMessage(), [
                    'exception' => $e,
                    'request_data' => $request->all()
                ]);
                
                $msg = config('app.debug') ? 'Erreur lors de l\'enregistrement direct: ' . $e->getMessage() : "Une erreur technique est survenue lors de l'enregistrement. Merci de réessayer ou contacter l'administrateur.";
                return back()->withInput()->with('error', $msg);
            }
        }
        
        // Déboguer toutes les données soumises
        if ($request->has('debug')) {
            // Débogage avancé avec analyse des données des produits
            $produitsAnalysis = [];
            if ($request->has('produits') && is_array($request->produits)) {
                foreach ($request->produits as $index => $produit) {
                    $produitsAnalysis[$index] = [
                        'produit_id' => $produit['produit_id'] ?? 'manquant',
                        'quantite' => $produit['quantite'] ?? 'manquant',
                        'prix_unitaire' => $produit['prix_unitaire'] ?? 'manquant',
                        'date_peremption' => $produit['date_peremption'] ?? 'manquant',
                        'type_quantite' => gettype($produit['quantite'] ?? null),
                        'type_prix' => gettype($produit['prix_unitaire'] ?? null)
                    ];
                }
            }
            
            dd([
                'all_request_data' => $request->all(),
                'commande_id' => $request->input('commande_id'),
                'fournisseur_id' => $request->input('fournisseur_id'),
                'date_reception' => $request->input('date_reception'),
                'mode_paiement' => $request->input('mode_paiement'),
                'produits_brut' => $request->input('produits'),
                'produits_analysis' => $produitsAnalysis,
                'methode' => $request->method(),
                'session_errors' => session('errors') ? session('errors')->getBag('default')->all() : null
            ]);
        }

        try {
            $validated = $request->validate([
                'fournisseur_id' => 'required|exists:fournisseurs,id',
                'date_reception' => 'required|date',
                'numero_facture' => 'nullable|string|max:50',
                'mode_paiement' => 'required|in:especes,cheque,virement,autre',
                'description' => 'nullable|string',
                'produits' => 'required|array|min:1',
                'produits.*.produit_id' => 'required|exists:produits,id',
                'produits.*.quantite' => 'required|numeric|min:0.01',
                'produits.*.prix_unitaire' => 'required|numeric|min:0',
                'produits.*.date_peremption' => 'nullable|date|after:today'
            ]);
            
            DB::beginTransaction();

            // Créer la réception
            $reception = Reception::create([
                'numero' => 'REC-' . strtoupper(Str::random(8)),
                'fournisseur_id' => $validated['fournisseur_id'],
                'user_id' => null,
                'date_reception' => $validated['date_reception'],
                'numero_facture' => $validated['numero_facture'],
                'mode_paiement' => $validated['mode_paiement'],
                'description' => $validated['description'],
                'statut' => Reception::STATUT_TERMINEE
            ]);

            // Créer les détails de réception
            foreach ($validated['produits'] as $produit) {
                DetailReception::create([
                    'reception_id' => $reception->id,
                    'produit_id' => $produit['produit_id'],
                    'quantite' => $produit['quantite'],
                    'prix_unitaire' => $produit['prix_unitaire'],
                    'date_peremption' => $produit['date_peremption'] ?? null
                ]);

                // Mettre à jour le stock du produit
                $produit_obj = Produit::find($produit['produit_id']);
                $produit_obj->increment('stock', $produit['quantite']);
            }

            DB::commit();
            
            // Message de succès détaillé
            $message = 'Réception n°' . $reception->numero . ' enregistrée avec succès. ';
            $message .= count($validated['produits']) . ' produits réceptionnés.';
            
            return redirect()->route('receptions.show', $reception)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Journalisation de l'erreur complète
            \Log::error('Erreur création réception: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement de la réception : ' . $e->getMessage());
        }
    }

    public function show(Reception $reception)
    {
        $reception->load(['fournisseur', 'user', 'details.produit']);
        return view('receptions.show', compact('reception'));
    }

    public function edit(Reception $reception)
    {
        if ($reception->statut !== 'en_cours') {
            return redirect()->route('receptions.show', $reception)
                ->with('error', 'Cette réception ne peut plus être modifiée.');
        }

        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $produits = Produit::orderBy('nom')->get();
        $reception->load('details');

        return view('receptions.edit', compact('reception', 'fournisseurs', 'produits'));
    }

    public function update(Request $request, Reception $reception)
    {
        if ($reception->statut !== 'en_cours') {
            return redirect()->route('receptions.show', $reception)
                ->with('error', 'Cette réception ne peut plus être modifiée.');
        }

        // On accepte soit 'produits' (création), soit 'details' (édition)
        if ($request->has('produits')) {
            $validated = $request->validate([
                'fournisseur_id' => 'required|exists:fournisseurs,id',
                'date_reception' => 'required|date',
                'numero_facture' => 'nullable|string|max:50',
                'mode_paiement' => 'required|in:especes,cheque,virement,autre',
                'description' => 'nullable|string',
                'produits' => 'required|array|min:1',
                'produits.*.produit_id' => 'required|exists:produits,id',
                'produits.*.quantite' => 'required|numeric|min:0.01',
                'produits.*.prix_unitaire' => 'required|numeric|min:0',
                'produits.*.date_peremption' => 'nullable|date|after:today'
            ]);
            $produits = $validated['produits'];
        } else {
            $validated = $request->validate([
                'fournisseur_id' => 'required|exists:fournisseurs,id',
                'date_reception' => 'required|date',
                'numero_facture' => 'nullable|string|max:50',
                'mode_paiement' => 'required|in:especes,cheque,virement,autre',
                'description' => 'nullable|string',
                'details' => 'required|array|min:1',
                'details.*.quantite' => 'required|numeric|min:0.01',
                'details.*.prix_unitaire' => 'required|numeric|min:0',
                'details.*.date_peremption' => 'nullable|date|after:today'
            ]);
            // Reconstruire $produits à partir de $reception->details et des inputs
            $produits = [];
            foreach ($reception->details as $detail) {
                $id = $detail->id;
                if (isset($validated['details'][$id])) {
                    $produits[] = [
                        'produit_id' => $detail->produit_id,
                        'quantite' => $validated['details'][$id]['quantite'],
                        'prix_unitaire' => $validated['details'][$id]['prix_unitaire'],
                        'date_peremption' => $validated['details'][$id]['date_peremption'] ?? null
                    ];
                }
            }
        }

        try {
            DB::beginTransaction();

            // Restaurer les anciens stocks
            foreach ($reception->details as $detail) {
                $produit_obj = Produit::find($detail->produit_id);
                $produit_obj->decrement('stock', $detail->quantite);
            }

            // Supprimer les anciens détails
            $reception->details()->delete();

            // Mettre à jour la réception
            $reception->update([
                'fournisseur_id' => $validated['fournisseur_id'],
                'date_reception' => $validated['date_reception'],
                'numero_facture' => $validated['numero_facture'] ?? null,
                'mode_paiement' => $validated['mode_paiement'],
                'description' => $validated['description'] ?? null,
                'statut' => Reception::STATUT_TERMINEE
            ]);

            // Créer les nouveaux détails
            foreach ($validated['produits'] as $produit) {
                DetailReception::create([
                    'reception_id' => $reception->id,
                    'produit_id' => $produit['produit_id'],
                    'quantite' => $produit['quantite'],
                    'prix_unitaire' => $produit['prix_unitaire'],
                    'date_peremption' => $produit['date_peremption'] ?? null
                ]);

                // Mettre à jour les nouveaux stocks
                $produit_obj = Produit::find($produit['produit_id']);
                $produit_obj->increment('stock', $produit['quantite']);
            }

            DB::commit();
            return redirect()->route('receptions.show', $reception)
                ->with('success', 'Réception mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            $msg = config('app.debug') ? 'Erreur lors de la mise à jour de la réception : ' . $e->getMessage() : "Une erreur technique est survenue lors de la mise à jour. Merci de réessayer ou contacter l'administrateur.";
            return back()->withInput()->with('error', $msg);
        }
    }

    public function destroy(Reception $reception)
    {
        if ($reception->statut !== 'en_cours') {
            return redirect()->route('receptions.show', $reception)
                ->with('error', 'Cette réception ne peut plus être supprimée.');
        }

        try {
            DB::beginTransaction();

            // Restaurer les stocks
            foreach ($reception->details as $detail) {
                $produit_obj = Produit::find($detail->produit_id);
                $produit_obj->decrement('stock', $detail->quantite);
            }

            // Supprimer la réception et ses détails
            $reception->details()->delete();
            $reception->delete();

            DB::commit();
            return redirect()->route('receptions.index')
                ->with('success', 'Réception supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            $msg = config('app.debug') ? 'Erreur lors de la suppression de la réception : ' . $e->getMessage() : "Une erreur technique est survenue lors de la suppression. Merci de réessayer ou contacter l'administrateur.";
            return back()->with('error', $msg);
        }
    }
}
