<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caisse;
use App\Models\Vente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\FacadePdf;

class CaisseController extends Controller
{
    /**
     * Constructeur avec vérification des permissions
     */
    public function __construct()
    {
        // Vérifier que l'utilisateur a le rôle de caissier ou admin
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            
            $user = Auth::user();
            
            // Vérifier si l'utilisateur a les permissions nécessaires
            if (!$user->hasRole(['caissier', 'admin', 'super-admin', 'super_admin'])) {
                Log::warning('Tentative d\'accès non autorisé au module caisse', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip()
                ]);
                
                abort(403, "Vous n'avez pas les autorisations nécessaires pour accéder à cette section.");
            }
            
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Vérifier explicitement la permission
        if (!Auth::user()->hasPermissionTo('caisse.access')) {
            Log::warning('Tentative d\'accès non autorisé à l\'interface caisse', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email
            ]);
            
            abort(403, "Vous n'avez pas la permission d'accéder à l'interface caisse.");
        }

        // Récupérer l'ID de la caisse depuis la session
        $caisseId = session('caisse_id');

        // Si aucune caisse n'est active en session, rediriger vers la page de connexion de la caisse
        if (!$caisseId) {
            // return redirect()->route('caisse.login'); // Commenté pour enlever la redirection
             // Au lieu de rediriger, on pourrait afficher un message ou charger une vue différente si besoin
             // Pour l'instant, on va juste continuer sans caisse sélectionnée.
        }

        $caisse = Caisse::find($caisseId);
        $caisseSoldeGlobal = 0;

        if ($caisse) {
            // Récupérer le solde global de la caisse active
            $caisseSoldeGlobal = $caisse->solde;
        } else {
            // Si l'ID en session ne correspond à aucune caisse existante, vider la session et rediriger
            session()->forget('caisse_id');
             // return redirect()->route('caisse.login')->with('error', 'La caisse sélectionnée n\'existe plus.'); // Commenté
             // Gérer le cas où la caisse n'existe plus, par exemple, afficher un message d'erreur ou réinitialiser.
             $caisseSoldeGlobal = 0; // Réinitialiser le solde si la caisse n'existe plus
             // Ajouter un message flash si souhaité :
             // session()->flash('error', 'La caisse sélectionnée n\'existe plus.');
        }

        $operations = Caisse::with(['user', 'ventes'])
            ->latest() // Tri par date de création descendante
            ->paginate(15);

        // Calculer les totaux des entrées et sorties pour la journée en cours
        $debutJour = now()->startOfDay();
        $finJour = now()->endOfDay();

        $entreesJour = Caisse::where('type_operation', 'entree')
                              // Filtrer par caisse active
                             ->whereBetween('created_at', [$debutJour, $finJour])
                             ->sum('montant');

        $sortiesJour = Caisse::where('type_operation', 'sortie')
                               // Filtrer par caisse active
                              ->whereBetween('created_at', [$debutJour, $finJour])
                              ->sum('montant');

        // Le solde calculé ici est uniquement pour la page actuelle des opérations paginées.
        $entreesPage = $operations->where('type_operation', 'entree')->sum('montant');
        $sortiesPage = $operations->where('type_operation', 'sortie')->sum('montant');
        $soldePage = $entreesPage - $sortiesPage; // Solde de la page actuelle

        // Préparer les données pour le graphique d'évolution du solde (pour la caisse active)
        // Récupérer les opérations de la caisse active sur une période définie (ex: 30 derniers jours)
        $periodeDebut = now()->subDays(30)->startOfDay();
        $periodeFin = now()->endOfDay();

        // Tentative de forcer le rafraîchissement du cache (Opcache)
        $operationsPourGraphique = Caisse::whereBetween('created_at', [$periodeDebut, $periodeFin])
            ->orderBy('created_at')
            ->get();

        // Calculer le solde cumulé jour par jour
        $soldeCumule = $operationsPourGraphique->isEmpty() ? 0 : Caisse::where('created_at', '<', $periodeDebut)->sum('montant'); // Solde avant la période du graphique

        $donneesGraphique = $operationsPourGraphique->map(function ($operation) use (&$soldeCumule) {
            if ($operation->type_operation === 'entree') {
                $soldeCumule += $operation->montant;
            } elseif ($operation->type_operation === 'sortie') {
                $soldeCumule -= $operation->montant;
            }
            return [
                'date' => $operation->created_at->format('Y-m-d'),
                'solde' => $soldeCumule,
            ];
        })->groupBy('date')->map(function ($dailyOperations) {
            // Prendre le dernier solde cumulé de la journée
            return $dailyOperations->last()['solde'];
        })->toArray();

        // Préparer les labels (dates) et les données (solde cumulé) pour le graphique
        $labelsGraphique = array_keys($donneesGraphique);
        $dataGraphique = array_values($donneesGraphique);

        return view('caisse.index', compact('operations', 'soldePage', 'entreesPage', 'sortiesPage', 'caisse', 'caisseSoldeGlobal', 'entreesJour', 'sortiesJour', 'labelsGraphique', 'dataGraphique'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Vérifier explicitement la permission
        if (!Auth::user()->hasPermissionTo('caisse.operate')) {
            Log::warning('Tentative d\'opération non autorisée sur la caisse', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'operation' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => "Vous n'avez pas la permission d'effectuer des opérations de caisse."
            ], 403);
        }

        try {
            $validated = $request->validate([
                'type_operation' => 'required|in:entree,sortie',
                'montant' => 'required|numeric|min:1',
                'mode_paiement' => 'required|string',
                'description' => 'nullable|string|max:500',
                'vente_id' => 'nullable|exists:ventes,id',
                'caisse_id' => 'required|exists:caisses,id'
            ]);

            $validated['user_id'] = null; // Pas d'utilisateur requis
            $validated['numero'] = Caisse::genererNumeroOperation();
            
            $caisse = Caisse::findOrFail($request->caisse_id);
            if ($validated['type_operation'] === 'entree') {
                $caisse->solde += $validated['montant'];
            } else {
                if (!$caisse->canPerformOperation($validated['montant'], 'sortie')) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Solde insuffisant pour effectuer cette sortie. Solde actuel : ' . number_format($caisse->solde, 0, ',', ' ') . ' FCFA');
                }
                $caisse->solde -= $validated['montant'];
            }
            $caisse->save();

            $operation = Caisse::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Opération enregistrée avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vente $vente)
    {
        // Vérifier explicitement la permission
        if (!Auth::user()->hasPermissionTo('caisse.view')) {
            Log::warning('Tentative de consultation non autorisée d\'une vente en caisse', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'vente_id' => $vente->id
            ]);
            
            abort(403, "Vous n'avez pas la permission de consulter les détails de cette vente.");
        }

        if (!session()->has('caisse_id')) {
            // return redirect()->route('caisse.login'); // Commenté pour enlever la redirection
            // Gérer le cas où l'ID de caisse est manquant en session.
        }

        $caisse = Caisse::findOrFail($vente->caisse_id);
        
        if ($caisse->id !== session('caisse_id')) {
            return redirect()->route('caisse.dashboard')
                ->with('error', 'Vous n\'avez pas accès à cette caisse.');
        }

        return view('caisse.show', compact('vente', 'caisse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function imprimerRecuOperation(Caisse $caisse)
    {
        // Vérifier explicitement la permission
        if (!Auth::user()->hasPermissionTo('caisse.print')) {
            Log::warning('Tentative d\'impression non autorisée d\'un reçu de caisse', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'caisse_id' => $caisse->id
            ]);
            
            abort(403, "Vous n'avez pas la permission d'imprimer des reçus de caisse.");
        }

        $caisse->load(['user', 'ventes']);
        return view('caisse.recu_operation', compact('caisse'));
    }

    public function rapport(Request $request)
    {
        // Vérifier explicitement la permission
        if (!Auth::user()->hasPermissionTo('caisse.report')) {
            Log::warning('Tentative d\'accès non autorisé au rapport de caisse', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email
            ]);
            
            abort(403, "Vous n'avez pas la permission d'accéder aux rapports de caisse.");
        }

        // Récupérer l'ID de la caisse depuis la session
        $caisseId = session('caisse_id');

        // Si aucune caisse n'est active en session, rediriger vers la page de connexion de la caisse
        if (!$caisseId) {
            // return redirect()->route('caisse.login'); // Commenté
             // Gérer le cas où aucune caisse n'est sélectionnée.
        }

        $dateDebut = $request->input('date_debut', now()->startOfMonth()->toDateString());
        $dateFin = $request->input('date_fin', now()->endOfMonth()->toDateString());
        $typeOperation = $request->input('type_operation');
        $modePaiement = $request->input('mode_paiement');

        $rapportOperations = Caisse::query();

        if ($caisseId) {
            $rapportOperations;
        }

        $rapportOperations->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59']);

        if ($typeOperation) {
            $rapportOperations->where('type_operation', $typeOperation);
        }

        if ($modePaiement) {
            $rapportOperations->where('mode_paiement', $modePaiement);
        }

        $rapportOperations = $rapportOperations->orderBy('created_at', 'desc')->get();

        // Calculer les totaux pour le rapport
        $totalEntrees = $rapportOperations->where('type_operation', 'entree')->sum('montant');
        $totalSorties = $rapportOperations->where('type_operation', 'sortie')->sum('montant');
        $soldePeriode = $totalEntrees - $totalSorties;

        // Passer les données et les filtres appliqués à la vue
        return view('caisse.rapport', compact('rapportOperations', 'totalEntrees', 'totalSorties', 'soldePeriode', 'dateDebut', 'dateFin', 'typeOperation', 'modePaiement'));
    }

    public function imprimerRapport(Request $request)
    {
         // Récupérer l'ID de la caisse depuis la session
         $caisseId = session('caisse_id');

         // Si aucune caisse n'est active en session, rediriger vers la page de connexion de la caisse
         if (!$caisseId) {
             // return redirect()->route('caisse.login'); // Commenté
              // Gérer le cas où aucune caisse n'est sélectionnée.
         }

         $dateDebut = $request->input('date_debut', now()->startOfMonth()->toDateString());
         $dateFin = $request->input('date_fin', now()->endOfMonth()->toDateString());
         $typeOperation = $request->input('type_operation');
         $modePaiement = $request->input('mode_paiement');

         $rapportOperations = Caisse::query();

         if ($caisseId) {
             $rapportOperations;
         }

         $rapportOperations->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59']);

         if ($typeOperation) {
             $rapportOperations->where('type_operation', $typeOperation);
         }

         if ($modePaiement) {
             $rapportOperations->where('mode_paiement', $modePaiement);
         }

         $rapportOperations = $rapportOperations->orderBy('created_at', 'desc')->get();

         // Calculer les totaux pour le rapport
         $totalEntrees = $rapportOperations->where('type_operation', 'entree')->sum('montant');
         $totalSorties = $rapportOperations->where('type_operation', 'sortie')->sum('montant');
         $soldePeriode = $totalEntrees - $totalSorties;

         $pdf = FacadePdf::loadView('rapports.caisse', compact('rapportOperations', 'totalEntrees', 'totalSorties', 'soldePeriode', 'dateDebut', 'dateFin', 'typeOperation', 'modePaiement'));

         // Optionnel: Télécharger le PDF
         // return $pdf->download('rapport_caisse_' . $dateDebut . '_a_' . $dateFin . '.pdf');

         // Optionnel: Afficher le PDF dans le navigateur
         return $pdf->stream('rapport_caisse_' . $dateDebut . '_a_' . $dateFin . '.pdf');
    }

     // Méthode pour simuler la sélection d'une caisse (pour le développement/test)
     public function selectCaisse($caisseId)
     {
         $caisse = Caisse::find($caisseId);
         if ($caisse) {
             session(['caisse_id' => $caisse->id]);
             return redirect()->route('caisse.index')->with('success', 'Caisse sélectionnée avec succès.');
         } else {
             return redirect()->back()->with('error', 'Caisse introuvable.');
         }
     }

     // Méthode pour vider la caisse sélectionnée (pour le développement/test)
     public function unsetCaisse()
     {
         session()->forget('caisse_id');
         return redirect()->route('caisse.index')->with('success', 'Caisse désélectionnée.');
     }


}
