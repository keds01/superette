<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Remise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RemiseController extends Controller
{
    public function __construct()
    {
    
        // Note: Les middlewares ont été déplacés dans routes/web.php pour compatibilité avec Laravel 12
}

    /**
     * Affiche le formulaire de création d'une remise
     */
    public function create()
    {
        // Version simplifiée pour déboguer le problème de page blanche
        $types_remise = [
            Remise::TYPE_POURCENTAGE => 'Pourcentage',
            Remise::TYPE_MONTANT_FIXE => 'Montant fixe'
        ];
        
        // Simplification pour éviter les problèmes potentiels avec les relations
        try {
            // Comptons d'abord le nombre de ventes disponibles pour le débogage
            $ventesCount = Vente::where('statut', '!=', Vente::STATUT_ANNULEE)->count();
            
            // Récupérons les ventes
            $ventes = Vente::where('statut', '!=', Vente::STATUT_ANNULEE)
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
            
            // Ajoutons un message de session pour déboguer
            session()->flash('debug_info', "Nombre de ventes disponibles: {$ventesCount}");
                          
            return view('remises.create', compact('types_remise', 'ventes'));
        } catch (\Exception $e) {
            // Répondre avec une page simple en cas d'erreur
            session()->flash('error', "Erreur: {$e->getMessage()}");
            return redirect()->route('dashboard')->with('error', "Erreur lors du chargement des remises: {$e->getMessage()}");
        }
    }

    /**
     * Affiche la liste des remises avec filtres
     */
    public function index(Request $request)
    {
        try {
            $query = Remise::with('vente')->orderBy('created_at', 'desc');

            // Appliquer les filtres
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('code_remise', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhereHas('vente', function($q) use ($search) {
                            $q->where('id', 'LIKE', "%{$search}%");
                        });
                });
            }

            if ($request->has('type') && !empty($request->type)) {
                $query->where('type_remise', $request->type);
            }

            if ($request->has('statut')) {
                if ($request->statut == 'active') {
                    $query->where('actif', true);
                } elseif ($request->statut == 'inactive') {
                    $query->where('actif', false);
                }
            }

            // Filtrage par superette si nécessaire (multi-superette)
            if (session()->has('active_superette_id')) {
                $query->whereHas('vente', function($q) {
                    $q->where('superette_id', session('active_superette_id'));
                });
            }

            $remises = $query->paginate(15);
            return view('remises.index', compact('remises'));
        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors du chargement des remises: {$e->getMessage()}");
        }
    }

    /**
     * Enregistre une nouvelle remise
     */
    public function store(Request $request, Vente $vente)
    {
        $request->validate([
            'type_remise' => 'required|in:' . implode(',', [
                Remise::TYPE_POURCENTAGE,
                Remise::TYPE_MONTANT_FIXE
            ]),
            'valeur_remise' => 'required|numeric|min:0.01',
            'code_remise' => 'nullable|string|max:50|unique:remises,code_remise',
            'description' => 'nullable|string|max:500'
        ]);

        // Vérifier la valeur maximale de la remise
        if ($request->type_remise === Remise::TYPE_POURCENTAGE && $request->valeur_remise > 100) {
            return back()->withInput()
                ->with('error', 'Le pourcentage de remise ne peut pas dépasser 100%.');
        }

        if ($request->type_remise === Remise::TYPE_MONTANT_FIXE && $request->valeur_remise > $vente->montant_total) {
            return back()->withInput()
                ->with('error', 'Le montant de la remise ne peut pas dépasser le montant total de la vente.');
        }

        try {
            DB::beginTransaction();

            $remise = new Remise([
                'type_remise' => $request->type_remise,
                'valeur_remise' => $request->valeur_remise,
                'code_remise' => $request->code_remise,
                'description' => $request->description,
                'actif' => true
            ]);

            $vente->remises()->save($remise);

            // Calculer le montant de la remise
            $remise->calculerMontantRemise($vente->montant_total);

            DB::commit();
            return redirect()->route('ventes.show', $vente)
                ->with('success', 'Remise appliquée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de l\'application de la remise : ' . $e->getMessage());
        }
    }

    /**
     * Met à jour une remise
     */
    public function update(Request $request, Remise $remise)
    {
        $request->validate([
            'type_remise' => 'required|in:' . implode(',', [
                Remise::TYPE_POURCENTAGE,
                Remise::TYPE_MONTANT_FIXE
            ]),
            'valeur_remise' => 'required|numeric|min:0.01',
            'code_remise' => 'nullable|string|max:50|unique:remises,code_remise,' . $remise->id,
            'description' => 'nullable|string|max:500'
        ]);

        // Vérifier la valeur maximale de la remise
        if ($request->type_remise === Remise::TYPE_POURCENTAGE && $request->valeur_remise > 100) {
            return back()->withInput()
                ->with('error', 'Le pourcentage de remise ne peut pas dépasser 100%.');
        }

        if ($request->type_remise === Remise::TYPE_MONTANT_FIXE && $request->valeur_remise > $remise->vente->montant_total) {
            return back()->withInput()
                ->with('error', 'Le montant de la remise ne peut pas dépasser le montant total de la vente.');
        }

        try {
            DB::beginTransaction();

            $remise->update([
                'type_remise' => $request->type_remise,
                'valeur_remise' => $request->valeur_remise,
                'code_remise' => $request->code_remise,
                'description' => $request->description
            ]);

            // Recalculer le montant de la remise
            $remise->calculerMontantRemise($remise->vente->montant_total);

            DB::commit();
            return redirect()->route('ventes.show', $remise->vente)
                ->with('success', 'Remise mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la remise : ' . $e->getMessage());
        }
    }

    /**
     * Supprime une remise
     */
    public function destroy(Remise $remise)
    {
        try {
            DB::beginTransaction();

            $vente = $remise->vente;
            $remise->delete();

            DB::commit();
            return redirect()->route('ventes.show', $vente)
                ->with('success', 'Remise supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression de la remise : ' . $e->getMessage());
        }
    }

    /**
     * Vérifie la validité d'un code de remise
     */
    public function verifierCode(Request $request)
    {
        $request->validate([
            'code_remise' => 'required|string|max:50'
        ]);

        $remise = Remise::where('code_remise', $request->code_remise)->first();

        if (!$remise) {
            return response()->json([
                'success' => false,
                'message' => 'Code de remise invalide.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'remise' => [
                'type' => $remise->type_remise,
                'valeur' => $remise->valeur_remise,
                'description' => $remise->description
            ]
        ]);
    }

    /**
     * Affiche la sélection de vente avant création de remise
     */
    public function selectVente(Request $request)
    {
        $query = Vente::where('statut', '!=', Vente::STATUT_ANNULEE);
        
        // Filtrage par superette active (multi-superette)
        if (session()->has('active_superette_id')) {
            $query->where('superette_id', session('active_superette_id'));
        }
        
        // Recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function($q) use ($search) {
                        $q->where('nom', 'LIKE', "%{$search}%")
                            ->orWhere('prenom', 'LIKE', "%{$search}%");
                    });
            });
        }
        
        // Filtrage par date
        if ($request->has('date_debut') && !empty($request->date_debut)) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin') && !empty($request->date_fin)) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        
        // Filtrage par montant
        if ($request->has('montant_min') && !empty($request->montant_min)) {
            $query->where('montant_total', '>=', $request->montant_min);
        }
        
        // Charger les relations et paginer
        $ventes = $query->with('client')
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);
        
        return view('remises.select-vente', compact('ventes'));
    }

    /**
     * Affiche le formulaire de création de remise pour une vente donnée
     */
    public function createForVente(\App\Models\Vente $vente)
    {
        $types_remise = [
            \App\Models\Remise::TYPE_POURCENTAGE => 'Pourcentage',
            \App\Models\Remise::TYPE_MONTANT_FIXE => 'Montant fixe'
        ];
        return view('remises.create', compact('types_remise', 'vente'));
    }
} 