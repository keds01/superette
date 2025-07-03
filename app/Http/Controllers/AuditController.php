<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Vente;
use App\Models\User;
use App\Models\Produit;
use App\Models\Paiement;
use App\Models\Commentaire;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MouvementStock;

class AuditController extends Controller
{
    protected $auditService;
    
    /**
     * IMPORTANT: Laravel 12 ne supporte plus l'appel direct à middleware() dans les contrôleurs
     * Les restrictions d'accès sont maintenant configurées dans le fichier routes/web.php
     *
     * @param AuditService $auditService
     */
    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
        // AUCUN APPEL À middleware() ICI - Ce n'est plus supporté dans Laravel 12
    }
    
    /**
     * Affiche le tableau de bord d'audit
     */
    public function index()
    {
        try {
            // Récupération de la supérette active
            $superetteId = session('active_superette_id');

            // Récupération des statistiques générales (en tenant compte de la supérette)
            $totalActivites = \App\Models\ActivityLog::withoutGlobalScopes()
                ->when($superetteId, fn($q) => $q->where('superette_id', $superetteId))
                ->count();
            
            // Récupération des dernières activités
            $dernieresActivites = \App\Models\ActivityLog::withoutGlobalScopes()
                ->when($superetteId, fn($q) => $q->where('superette_id', $superetteId))
                ->with('user')
                ->latest()
                ->take(10)
                ->get();
            
            // Récupération des anomalies récentes
            $anomalies = $this->auditService->detecterAnomalies();
            
            // Génération du rapport quotidien
            $rapportQuotidien = $this->auditService->genererRapportQuotidien(Carbon::today());
            
            // Calcul direct du chiffre d'affaires du jour pour s'assurer qu'il est à jour
            $query = Vente::whereDate('date_vente', Carbon::today())
                ->whereIn('statut', ['completee', 'terminee']);
                
            // Filtrer par supérette si spécifiée
            if ($superetteId) {
                $query->where('superette_id', $superetteId);
            }
            
            $ventesAujourdhui = $query->get();
            $chiffreAffairesJour = $ventesAujourdhui->sum('montant_total');
            
            // Mise à jour du chiffre d'affaires dans le rapport quotidien
            $rapportQuotidien['ventes']['montant_total'] = $chiffreAffairesJour;
            
            // Statistiques supplémentaires pour enrichir l'affichage
            $statsSupplementaires = [
                'total_ventes' => Vente::count(),
                'ventes_aujourd_hui' => Vente::whereDate('created_at', today())->count(),
                'total_produits' => Produit::count(),
                'produits_en_rupture' => Produit::where('stock', '<=', 0)->count(),
                'produits_en_alerte' => Produit::where('stock', '<=', DB::raw('seuil_alerte'))->where('stock', '>', 0)->count(),
                'total_utilisateurs' => User::count(),
                'activites_ce_mois' => ActivityLog::whereMonth('created_at', now()->month)->count(),
                'ventes_annulees' => Vente::where('statut', 'annulee')->count(),
                'mouvements_stock' => MouvementStock::count(),
            ];
            
            // Si pas d'activités, créer quelques exemples pour démonstration
            if ($totalActivites === 0) {
                $this->creerActivitesExemple();
                $totalActivites = ActivityLog::count();
                $dernieresActivites = ActivityLog::with('user')->latest()->take(10)->get();
            }
            
        } catch (\Exception $e) {
            // Log de l'erreur
            \Illuminate\Support\Facades\Log::error('Erreur dashboard audit: ' . $e->getMessage());
            
            // Valeurs par défaut
            $totalActivites = 0;
            $dernieresActivites = collect([]);
            $anomalies = [];
            $rapportQuotidien = [
                'date' => now()->format('Y-m-d'),
                'ventes' => [
                    'total' => 0, 
                    'montant_total' => 0, 
                    'panier_moyen' => 0,
                    'annulees' => ['total' => 0, 'montant_total' => 0]
                ],
                'paiements' => ['total' => 0, 'par_methode' => []],
                'activites' => ['total' => 0]
            ];
            $statsSupplementaires = [
                'total_ventes' => 0,
                'ventes_aujourd_hui' => 0,
                'total_produits' => 0,
                'produits_en_rupture' => 0,
                'produits_en_alerte' => 0,
                'total_utilisateurs' => 0,
                'activites_ce_mois' => 0,
                'ventes_annulees' => 0,
                'mouvements_stock' => 0,
            ];
        }
        
        return view('audit.dashboard', compact(
            'totalActivites',
            'dernieresActivites',
            'anomalies',
            'rapportQuotidien',
            'statsSupplementaires'
        ));
    }
    
    /**
     * Crée des activités d'exemple pour démonstration
     */
    private function creerActivitesExemple()
    {
        $user = auth()->user();
        $types = ['connexion', 'modification', 'creation', 'suppression', 'consultation'];
        $descriptions = [
            'connexion' => 'Connexion au système',
            'modification' => 'Modification d\'un produit',
            'creation' => 'Création d\'une nouvelle vente',
            'suppression' => 'Suppression d\'un enregistrement',
            'consultation' => 'Consultation du rapport'
        ];
        
        for ($i = 0; $i < 15; $i++) {
            $type = $types[array_rand($types)];
            $date = now()->subHours(rand(1, 72));
            
            ActivityLog::create([
                'type' => $type,
                'description' => $descriptions[$type],
                'user_id' => $user->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode(['exemple' => true]),
                'created_at' => $date,
                'updated_at' => $date,
                'superette_id' => session('active_superette_id') ?? $user->superette_id
            ]);
        }
    }
    
    /**
     * Affiche le journal des activités avec filtres
     */
    public function journal(Request $request)
    {
        // Récupération de la superette active
        $superetteId = session('active_superette_id');
        
        $query = ActivityLog::with('user')->latest();
        
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        // Application des filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        
        $activites = $query->paginate(15)->withQueryString();
        
        // Récupération des types d'activités et des utilisateurs pour les filtres
        // Filtrer les types d'activités par superette
        $typesQuery = ActivityLog::distinct('type');
        if ($superetteId) {
            $typesQuery->where('superette_id', $superetteId);
        }
        $types = $typesQuery->pluck('type')->toArray();
        
        // Récupérer les utilisateurs qui ont des activités dans cette superette
        $usersQuery = User::orderBy('name');
        if ($superetteId) {
            $usersQuery->whereHas('activityLogs', function($query) use ($superetteId) {
                $query->where('superette_id', $superetteId);
            });
        }
        $users = $usersQuery->get();
        
        return view('audit.journal', compact('activites', 'types', 'users'));
    }
    
    /**
     * Exporte le journal des activités en PDF
     */
    public function exporterJournal(Request $request)
    {
        // Récupération de la superette active
        $superetteId = session('active_superette_id');
        
        $query = ActivityLog::with('user')->latest();
        
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        // Application des filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        
        $activites = $query->get();
        
        $pdf = PDF::loadView('audit.pdf.journal', [
            'activites' => $activites,
            'filtres' => $request->all(),
            'date_generation' => now()
        ]);
        
        return $pdf->download('journal-activites-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Affiche la liste des anomalies avec filtres
     */
    public function anomalies(Request $request)
    {
        // Récupération de la superette active
        $superetteId = session('active_superette_id');
        
        // Ajout du filtre par superette à la requête
        $filtres = $request->all();
        if ($superetteId) {
            $filtres['superette_id'] = $superetteId;
        }
        
        $anomalies = $this->auditService->getAnomaliesPaginated($filtres, 15);
        
        return view('audit.anomalies', ['anomalies' => $anomalies]);
    }
    
    /**
     * Exporte la liste des anomalies en PDF
     */
    public function exporterAnomalies(Request $request)
    {
        // Récupération de la superette active
        $superetteId = session('active_superette_id');
        
        // Ajout du filtre par superette à la requête
        $filtres = $request->all();
        if ($superetteId) {
            $filtres['superette_id'] = $superetteId;
        }
        
        $anomalies = $this->auditService->getAnomalies($filtres);
        
        $pdf = PDF::loadView('audit.pdf.anomalies', [
            'anomalies' => $anomalies,
            'filtres' => $filtres,
            'date_generation' => now()
        ]);
        
        return $pdf->download('anomalies-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Affiche le rapport quotidien d'audit
     */
    public function rapportQuotidien(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        // Génération du rapport quotidien pour la date spécifiée
        $rapportQuotidien = $this->auditService->genererRapportQuotidien($date);
        
        // Calcul direct du chiffre d'affaires du jour (sans les ventes annulées)
        $superetteId = session('active_superette_id');
        $query = Vente::whereDate('date_vente', $date)
            ->whereIn('statut', ['completee', 'terminee']); // Exclure les ventes annulées
            
        // Filtrer par supérette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        $ventesValides = $query->get();
        $chiffreAffairesJour = $ventesValides->sum('montant_total');
        
        // Mise à jour du chiffre d'affaires dans le rapport quotidien
        $rapportQuotidien['ventes']['montant_total'] = $chiffreAffairesJour;
        $rapportQuotidien['ventes']['panier_moyen'] = $ventesValides->count() > 0 ? $chiffreAffairesJour / $ventesValides->count() : 0;
        
        // Récupération des paiements avec méthodes correctes
        $queryPaiements = Paiement::whereDate('date_paiement', $date);
        if ($superetteId) {
            $queryPaiements->where('superette_id', $superetteId);
        }
        
        $paiements = $queryPaiements->get();
        $montantsParMethode = $paiements->groupBy('methode')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'total' => $items->sum('montant')
                ];
            })->toArray();
        
        // Mise à jour des informations de paiement dans le rapport
        $rapportQuotidien['paiements']['total'] = $paiements->count();
        $rapportQuotidien['paiements']['montant_total'] = $paiements->sum('montant');
        $rapportQuotidien['paiements']['par_methode'] = $montantsParMethode;
        $rapportQuotidien['paiements']['total_transactions'] = $paiements->count();
        $rapportQuotidien['paiements']['total_montant'] = $paiements->sum('montant');
        
        // Vérifier si l'export PDF est demandé
        if ($request->format === 'pdf') {
            $pdf = PDF::loadView('audit.pdf.rapport-quotidien', [
                'rapportQuotidien' => $rapportQuotidien,
                'date' => $date,
                'date_generation' => now()
            ]);
            
            return $pdf->download('rapport-quotidien-' . $date->format('Y-m-d') . '.pdf');
        }
        
        return view('audit.rapport-quotidien', compact('rapportQuotidien', 'date'));
    }
    
    /**
     * Affiche le détail d'une anomalie spécifique
     */
    public function detailAnomalie($id)
    {
        // Récupération de la superette active
        $superetteId = session('active_superette_id');
        
        $anomalie = $this->auditService->getAnomalieById($id);
        
        if (!$anomalie) {
            return redirect()->route('audit.anomalies')
                ->with('error', 'Anomalie non trouvée');
        }
        
        // Vérifier que l'anomalie appartient à la superette active
        if ($superetteId && isset($anomalie['superette_id']) && $anomalie['superette_id'] != $superetteId) {
            return redirect()->route('audit.anomalies')
                ->with('error', 'Vous n\'avez pas accès à cette anomalie');
        }
        
        // Récupération des données associées selon le type d'anomalie
        $produit = null;
        $utilisateur = null;
        $commentaires = [];
        $activitesLiees = [];
        
        if (isset($anomalie['details']['produit_id'])) {
            $produit = Product::find($anomalie['details']['produit_id']);
            $activitesQuery = ActivityLog::where('model_type', 'Product')
                ->where('model_id', $anomalie['details']['produit_id'])
                ->latest();
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $activitesQuery->where('superette_id', $superetteId);
            }
            
            $activitesLiees = $activitesQuery->take(5)->get();
        }
        
        if (isset($anomalie['details']['user_id'])) {
            $utilisateur = User::find($anomalie['details']['user_id']);
            $activitesQuery = ActivityLog::where('user_id', $anomalie['details']['user_id'])
                ->latest();
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $activitesQuery->where('superette_id', $superetteId);
            }
            
            $activitesLiees = $activitesQuery->take(5)->get();
        }
        
        if (isset($anomalie['details']['vente_id'])) {
            $activitesQuery = ActivityLog::where('model_type', 'Vente')
                ->where('model_id', $anomalie['details']['vente_id'])
                ->latest();
                
            // Filtrer par superette si spécifiée
            if ($superetteId) {
                $activitesQuery->where('superette_id', $superetteId);
            }
            
            $activitesLiees = $activitesQuery->take(5)->get();
        }
        
        // Récupération des commentaires associés à l'anomalie
        $commentaires = $this->auditService->getCommentairesByAnomalieId($id);
        
        return view('audit.detail-anomalie', compact(
            'anomalie',
            'produit',
            'utilisateur',
            'commentaires',
            'activitesLiees'
        ));
    }
    
    /**
     * Marque une anomalie comme étant en cours de traitement
     */
    public function marquerEnCours($id)
    {
        $result = $this->auditService->updateAnomalieStatus($id, 'en_cours');
        
        if ($result) {
            return redirect()->back()->with('success', 'Anomalie marquée comme en cours de traitement');
        }
        
        return redirect()->back()->with('error', 'Impossible de mettre à jour le statut de l\'anomalie');
    }
    
    /**
     * Marque une anomalie comme résolue
     */
    public function marquerResolue($id)
    {
        $result = $this->auditService->updateAnomalieStatus($id, 'resolue');
        
        if ($result) {
            return redirect()->back()->with('success', 'Anomalie marquée comme résolue');
        }
        
        return redirect()->back()->with('error', 'Impossible de mettre à jour le statut de l\'anomalie');
    }
    
    /**
     * Ignore une anomalie
     */
    public function ignorerAnomalie($id)
    {
        $result = $this->auditService->updateAnomalieStatus($id, 'ignoree');
        
        if ($result) {
            return redirect()->back()->with('success', 'Anomalie ignorée');
        }
        
        return redirect()->back()->with('error', 'Impossible de mettre à jour le statut de l\'anomalie');
    }
    
    /**
     * Ajoute un commentaire à une anomalie
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ajouterCommentaire(Request $request, $id)
    {
        // Validation de la requête
        $request->validate([
            'commentaire' => 'required|string|max:1000',
        ]);
        
        // Ajout du commentaire via le service
        $result = $this->auditService->addCommentToAnomalie($id, $request->commentaire, auth()->id());
        
        // Enregistrement dans le journal d'activités
        $this->auditService->logActivity(
            'traitement_anomalie',
            "Anomalie {$request->type} traitée avec action: {$request->action}",
            $request->type,
            $request->id,
            auth()->user(),
            [
                'action' => $request->action,
                'commentaire' => $request->commentaire
            ]
        );
        
        // Notification aux personnes concernées si nécessaire
        if ($request->action === 'escalate') {
            $gerants = User::whereHas('roles', function ($query) {
                $query->where('name', 'Gérant');
            })->pluck('email')->toArray();
            
            if (!empty($gerants)) {
                $this->auditService->notifierAnomalie([
                    'type' => $request->type,
                    'severite' => 'haute',
                    'message' => "Anomalie escaladée par " . auth()->user()->name,
                    'details' => [
                        'id' => $request->id,
                        'commentaire' => $request->commentaire
                    ]
                ], $gerants);
            }
        }
        
        // Redirection avec message approprié
        if ($result) {
            return redirect()->back()->with('success', 'Commentaire ajouté avec succès');
        }
        
        return redirect()->route('audit.anomalies')
            ->with('success', 'L\'anomalie a été traitée avec succès.');
    }
}