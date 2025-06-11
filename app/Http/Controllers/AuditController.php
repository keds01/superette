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
            // Récupération des statistiques générales
            $totalActivites = ActivityLog::count();
            
            // Récupération des dernières activités
            $dernieresActivites = ActivityLog::with('user')
                ->latest()
                ->take(10)
                ->get();
            
            // Récupération des anomalies récentes
            $anomalies = $this->auditService->detecterAnomalies();
            
            // Génération du rapport quotidien
            $rapportQuotidien = $this->auditService->genererRapportQuotidien();
            
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
        }
        
        return view('audit.dashboard', compact(
            'totalActivites',
            'dernieresActivites',
            'anomalies',
            'rapportQuotidien'
        ));
    }
    
    /**
     * Affiche le journal des activités avec filtres
     */
    public function journal(Request $request)
    {
        $query = ActivityLog::with('user')->latest();
        
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
        $types = ActivityLog::distinct('type')->pluck('type')->toArray();
        $users = User::orderBy('name')->get();
        
        return view('audit.journal', compact('activites', 'types', 'users'));
    }
    
    /**
     * Exporte le journal des activités en PDF
     */
    public function exporterJournal(Request $request)
    {
        $query = ActivityLog::with('user')->latest();
        
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
        $anomalies = $this->auditService->getAnomalies($request->all());
        
        // Paginer les résultats manuellement car c'est une collection et non un modèle
        $page = $request->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        $anomaliesPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $anomalies->slice($offset, $perPage),
            $anomalies->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('audit.anomalies', ['anomalies' => $anomaliesPaginator]);
    }
    
    /**
     * Exporte la liste des anomalies en PDF
     */
    public function exporterAnomalies(Request $request)
    {
        $anomalies = $this->auditService->getAnomalies($request->all());
        
        $pdf = PDF::loadView('audit.pdf.anomalies', [
            'anomalies' => $anomalies,
            'filtres' => $request->all(),
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
        $anomalie = $this->auditService->getAnomalieById($id);
        
        if (!$anomalie) {
            return redirect()->route('audit.anomalies')
                ->with('error', 'Anomalie non trouvée');
        }
        
        // Récupération des données associées selon le type d'anomalie
        $produit = null;
        $utilisateur = null;
        $commentaires = [];
        $activitesLiees = [];
        
        if (isset($anomalie['details']['produit_id'])) {
            $produit = Product::find($anomalie['details']['produit_id']);
            $activitesLiees = ActivityLog::where('model_type', 'Product')
                ->where('model_id', $anomalie['details']['produit_id'])
                ->latest()
                ->take(5)
                ->get();
        }
        
        if (isset($anomalie['details']['user_id'])) {
            $utilisateur = User::find($anomalie['details']['user_id']);
            $activitesLiees = ActivityLog::where('user_id', $anomalie['details']['user_id'])
                ->latest()
                ->take(5)
                ->get();
        }
        
        if (isset($anomalie['details']['vente_id'])) {
            $activitesLiees = ActivityLog::where('model_type', 'Vente')
                ->where('model_id', $anomalie['details']['vente_id'])
                ->latest()
                ->take(5)
                ->get();
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
