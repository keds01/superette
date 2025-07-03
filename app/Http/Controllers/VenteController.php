<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\VenteRequest;

class VenteController extends Controller
{
    /**
     * Affiche la liste des ventes
     */
    public function index(Request $request)
    {
        $superetteId = session('active_superette_id') ?? (auth()->user()->superette_id ?? null);
        $query = Vente::withoutGlobalScopes()
            ->where('superette_id', $superetteId)
            ->with([
                'client' => function($query) {
                    $query->withoutGlobalScopes();
                },
                'employe' => function($query) {
                    $query->withoutGlobalScopes();
                },
                'details' => function($query) {
                    $query->withoutGlobalScopes();
                }
            ])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('employe_id')) {
            $query->where('employe_id', $request->employe_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('date_vente', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_vente', '<=', $request->date_fin);
        }

        $ventes = $query->paginate(15);
        $clients = Client::withoutGlobalScopes()->get();
        $employes = Employe::withoutGlobalScopes()->get();

        // Statistiques rapides pour la vue (filtrées par superette)
        $totalVentes = Vente::withoutGlobalScopes()->where('superette_id', $superetteId)->count();
        $montantTotal = Vente::withoutGlobalScopes()->where('superette_id', $superetteId)->sum('montant_total');
        $montantPaye = Vente::withoutGlobalScopes()->where('superette_id', $superetteId)->sum('montant_paye');
        $clientsCount = Client::withoutGlobalScopes()->count();

        return view('ventes.index', compact('ventes', 'clients', 'employes', 'totalVentes', 'montantTotal', 'montantPaye', 'clientsCount'));
    }

    /**
     * Affiche le formulaire de création d'une vente
     */
    public function create()
    {
        $clients = Client::all();
        $employes = Employe::all();
        $produits = Produit::where('stock', '>', 0)
            ->with('conditionnements')
            ->get()
            ->map(function($produit) {
                // Prix promo (accessor) et promotion détaillée
                $produit->prix_promo = $produit->prix_promo;
                $promo = $produit->promotion_active;
                $produit->promotion = $promo ? [
                    'type' => $promo->type, // pourcentage ou montant
                    'valeur' => $promo->valeur,
                ] : null;
                
                // S'assurer que les conditionnements sont disponibles pour le frontend
                if (!isset($produit->conditionnements)) {
                    $produit->conditionnements = [];
                }
                
                return $produit;
            });
        
        // Debug des conditionnements
        \Log::debug('Produits pour la vue de vente', [
            'nombre_produits' => $produits->count(),
            'exemple_produit' => $produits->first() ? [
                'nom' => $produits->first()->nom,
                'conditionnements' => $produits->first()->conditionnements->count()
            ] : null
        ]);

        return view('ventes.create', compact('clients', 'employes', 'produits'));
    }

    /**
     * Enregistre une nouvelle vente
     */
    public function store(VenteRequest $request)
    {
        DB::beginTransaction();

        try {
            Log::info('VenteController@store - Données reçues:', $request->all());

            // Déterminer l'employé lié à l'utilisateur connecté (caissier)
            $employe_id = null;
            if (auth()->check()) {
                $employe_id = auth()->user()->employe?->id;

                // Si aucun employé associé, créer un enregistrement minimal
                if (!$employe_id) {
                    $nouvelEmploye = Employe::create([
                        'nom' => auth()->user()->name,
                        'user_id' => auth()->id(),
                        // Pas besoin d'email pour les employés créés automatiquement
                    ]);
                    $employe_id = $nouvelEmploye->id;
                }
            }

            // Sécurité : si toujours null, lever une exception explicite
            if (!$employe_id) {
                throw new \Exception('Impossible de déterminer l\'employé associé.');
            }

            // Récupérer la superette active
            $superette_id = session('active_superette_id') ?? (auth()->user()->superette_id ?? null);
            if (!$superette_id) {
                throw new \Exception('Impossible de déterminer la supérette active.');
            }

            $vente = Vente::create([
                'client_id' => $request->client_id,
                'employe_id' => $employe_id,
                'superette_id' => $superette_id,
                'type_vente' => $request->input('type_vente', 'vente'),
                'date_vente' => now(),
                'montant_total' => 0,
                'montant_paye' => $request->input('montant_paye', 0),
                'montant_restant' => 0,
                'statut' => 'terminee',
                'notes' => $request->input('notes', '')
            ]);

            $totalVente = 0;

            foreach ($request->produits as $produitData) {
                $produit = Produit::findOrFail($produitData['produit_id']);
                $quantite = $produitData['quantite'];
                $prixUnitaire = $produitData['prix_unitaire'];
                $sousTotal = $quantite * $prixUnitaire;

                DetailVente::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $produit->id,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'prix_achat_unitaire' => $produit->prix_achat_ht ?? 0,
                    'sous_total' => $sousTotal
                ]);

                $produit->decrement('stock', $quantite);
                $totalVente += $sousTotal;
            }

            $vente->update([
                'montant_total' => $totalVente,
                'montant_restant' => $totalVente - $vente->montant_paye
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente enregistrée avec succès.',
                'redirect' => route('ventes.show', $vente->id)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('VenteController@store - Erreur de validation: ' . $e->getMessage(), $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation lors de la création de la vente.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('VenteController@store - Erreur: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement de la vente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche les détails d'une vente
     */
    public function show($id)
    {
        // Utiliser withoutGlobalScopes pour ignorer le scope de superette
        $vente = Vente::withoutGlobalScopes()->findOrFail($id);
        
        // Charger les relations en désactivant les global scopes pour éviter les filtres par superette
        $vente->load([
            'client' => function($query) {
                $query->withoutGlobalScopes();
            },
            'employe' => function($query) {
                $query->withoutGlobalScopes();
            },
            'details.produit' => function($query) {
                $query->withoutGlobalScopes();
            },
            'remises' => function($query) {
                $query->withoutGlobalScopes();
            }
        ]);
        
        return view('ventes.show', compact('vente'));
    }

    /**
     * Affiche le formulaire d'édition d'une vente
     */
    public function edit(Vente $vente)
    {
        $vente->load('details.produit');
        $clients = Client::all();
        $employes = Employe::all();
        $produits = Produit::all();

        return view('ventes.edit', compact('vente', 'clients', 'employes', 'produits'));
    }

    /**
     * Met à jour une vente existante
     */
    public function update(Request $request, Vente $vente)
    {
        // Validation et logique de mise à jour
        // (À implémenter selon les besoins spécifiques)
    }

    /**
     * Supprime une vente
     */
    public function destroy(Vente $vente)
    {
        DB::beginTransaction();

        try {
            // Restaurer les stocks
            foreach ($vente->details as $detail) {
                $produit = $detail->produit;
                $produit->increment('stock', $detail->quantite);
            }

            // Supprimer les détails de la vente
            $vente->details()->delete();
            
            // Supprimer la vente
            $vente->delete();

            DB::commit();

            return redirect()
                ->route('ventes.index')
                ->with('success', 'Vente supprimée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression de la vente: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression de la vente');
        }
    }

    /**
     * Récupère les détails d'un produit pour le formulaire
     */
    public function getProductDetails($id)
    {
        $produit = Produit::findOrFail($id);
        return response()->json([
            'prix_vente' => $produit->prix_vente,
            'stock' => $produit->stock
        ]);
    }
    
    /**
     * Imprime un reçu pour une vente
     */
    public function recu(Vente $vente)
    {
        // Charger les relations sans appliquer le SuperetteScope
        $vente->load([
            'client' => function($query) {
                $query->withoutGlobalScopes();
            },
            'employe' => function($query) {
                $query->withoutGlobalScopes();
            },
            'details.produit' => function($query) {
                $query->withoutGlobalScopes();
            }
        ]);
        
        return view('ventes.recu', compact('vente'));
    }

    /**
     * Affiche et imprime la facture d'une vente
     */
    public function imprimerFacture(Vente $vente)
    {
        // Charger les relations sans appliquer le SuperetteScope
        $vente->load([
            'client' => function($query) {
                $query->withoutGlobalScopes();
            },
            'employe' => function($query) {
                $query->withoutGlobalScopes();
            },
            'details.produit' => function($query) {
                $query->withoutGlobalScopes();
            },
            'paiements'
        ]);
        
        return view('ventes.facture', compact('vente'));
    }
    
    /**
     * Méthode simplifiée pour enregistrer une vente sans passer par toutes les validations
     * Cette méthode est utilisée en secours quand la méthode normale échoue
     */
    public function storeExpress(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('VenteController@storeExpress - Utilisation du mode direct:', $request->all());

            // Déterminer l'employé lié à l'utilisateur connecté (caissier)
            $employe_id = null;
            if (auth()->check()) {
                $employe_id = auth()->user()->employe?->id;

                // Si aucun employé associé, créer un enregistrement minimal
                if (!$employe_id) {
                    $nouvelEmploye = Employe::create([
                        'nom' => auth()->user()->name,
                        'user_id' => auth()->id(),
                        // Pas besoin d'email pour les employés créés automatiquement
                    ]);
                    $employe_id = $nouvelEmploye->id;
                }
            }

            // Sécurité : si toujours null, lever une exception explicite
            if (!$employe_id) {
                throw new \Exception('Impossible de déterminer l\'employé associé.');
            }

            // Récupérer la superette active
            $superette_id = session('active_superette_id') ?? (auth()->user()->superette_id ?? null);
            if (!$superette_id) {
                throw new \Exception('Impossible de déterminer la supérette active.');
            }

            // Créer la vente directement
            $vente = new Vente();
            $vente->client_id = $request->input('client_id');
            $vente->employe_id = $employe_id;
            $vente->superette_id = $superette_id;
            // Forcer la valeur du champ type_vente à une valeur autorisée par la base de données
            $type_vente = $request->input('type_vente', 'sur_place');
            $types_autorises = ['sur_place', 'a_emporter', 'livraison'];
            $vente->type_vente = in_array($type_vente, $types_autorises) ? $type_vente : 'sur_place';
            $vente->date_vente = now();
            $vente->montant_total = 0; // Sera calculé plus tard
            $vente->montant_paye = $request->input('montant_paye', 0);
            $vente->montant_restant = 0; // Sera calculé plus tard
            $vente->statut = 'terminee';
            $vente->notes = $request->input('notes', '');
            $vente->save();

            $totalVente = 0;
            $produitsDonnees = $request->input('produits', []);

            // Vérification si les données des produits sont présentes
            if (empty($produitsDonnees)) {
                DB::rollBack();
                Log::error('VenteController@storeExpress - Aucun produit fourni');
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur: Aucun produit n\'a été fourni'
                ], 422);
            }

            foreach ($produitsDonnees as $produitData) {
                // Vérification des données minimales requises
                if (empty($produitData['produit_id']) || empty($produitData['quantite'])) {
                    continue; // Ignorer les produits sans ID ou quantité
                }

                $produit = Produit::find($produitData['produit_id']);
                if (!$produit) {
                    Log::warning('VenteController@storeExpress - Produit non trouvé: ' . $produitData['produit_id']);
                    continue; // Ignorer les produits qui n'existent pas
                }

                $quantite = floatval($produitData['quantite']);
                $prixUnitaire = !empty($produitData['prix_unitaire']) 
                    ? floatval($produitData['prix_unitaire']) 
                    : $produit->prix_vente;
                $sousTotal = $quantite * $prixUnitaire;

                // Créer le détail de vente
                $detail = new DetailVente();
                $detail->vente_id = $vente->id;
                $detail->produit_id = $produit->id;
                $detail->quantite = $quantite;
                $detail->prix_unitaire = $prixUnitaire;
                $detail->prix_achat_unitaire = $produit->prix_achat_ht ?? 0;
                $detail->sous_total = $sousTotal;
                $detail->save();

                // Mettre à jour le stock du produit
                if ($produit->stock >= $quantite) {
                    $produit->decrement('stock', $quantite);
                } else {
                    Log::warning('VenteController@storeExpress - Stock insuffisant pour: ' . $produit->nom);
                    // On permet la vente même si le stock est insuffisant
                    $produit->update(['stock' => 0]);
                }

                $totalVente += $sousTotal;
            }

            // Mettre à jour les montants de la vente
            $vente->montant_total = $totalVente;
            $vente->montant_restant = $totalVente - $vente->montant_paye;
            $vente->save();

            // Enregistrer le paiement si montant_paye > 0
            $montantPaye = floatval($request->input('montant_paye', 0));
            $modePaiement = $request->input('mode_paiement', 'especes');
            if ($montantPaye > 0) {
                \App\Models\Paiement::create([
                    'vente_id' => $vente->id,
                    'mode_paiement' => $modePaiement,
                    'montant' => $montantPaye,
                    'statut' => 'valide',
                    'date_paiement' => now(),
                ]);
            }

            DB::commit();

            Log::info('VenteController@storeExpress - Vente express réussie, ID: ' . $vente->id);
            return response()->json([
                'success' => true,
                'message' => 'Vente enregistrée avec succès en mode express.',
                'redirect' => route('ventes.show', $vente->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('VenteController@storeExpress - Erreur: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement de la vente express: ' . $e->getMessage()
            ], 500);
        }
    }
}
