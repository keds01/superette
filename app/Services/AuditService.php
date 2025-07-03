<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\Vente;
use App\Models\Paiement;
use App\Models\MouvementStock;
use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuditService
{
    /**
     * Enregistre une activité dans le journal
     * 
     * @param string $type Type d'activité (connexion, modification, etc.)
     * @param string $description Description de l'activité
     * @param string $model_type Type de modèle concerné
     * @param int|null $model_id ID du modèle concerné
     * @param User $user Utilisateur ayant effectué l'action
     * @param array $metadata Métadonnées additionnelles
     * @return ActivityLog
     */
    public function logActivity($type, $description, $model_type, $model_id, $user, $metadata = [])
    {
        $activityLog = new \App\Models\ActivityLog([
            'type' => $type,
            'description' => $description,
            'user_id' => $user->id,
            'model_type' => $model_type,
            'model_id' => $model_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => json_encode($metadata),
            'superette_id' => session('active_superette_id') ?? $user->superette_id
        ]);
        
        $activityLog->save();
        
        return $activityLog;
    }
    
    /**
     * Détecte des anomalies dans les activités récentes
     * 
     * @return array Liste des anomalies détectées
     */
    public function detecterAnomalies()
    {
        $anomalies = [];
        
        // Récupérer la supérette active
        $superetteId = session('active_superette_id');
        
        // 1. Variations de prix >10% en 24h
        $this->detecterVariationsPrix($anomalies, $superetteId);
        
        // 2. Séries d'annulations suspectes
        $this->detecterAnnulationsSuspectes($anomalies, $superetteId);
        
        // 3. Ajustements de stock massifs (>20%)
        $this->detecterAjustementsStockMassifs($anomalies, $superetteId);
        
        return $anomalies;
    }
    
    /**
     * Détecte les variations de prix importantes (>10%) en 24h
     * 
     * @param array &$anomalies Tableau d'anomalies à compléter
     */
    private function detecterVariationsPrix(&$anomalies, $superetteId)
    {
        // Récupérer les produits dont le prix a changé récemment
        $hier = Carbon::now()->subDay();
        $query = Produit::whereHas('mouvementsStock', function ($query) use ($hier) {
            $query->where('created_at', '>=', $hier)
                ->where('type', 'like', '%ajustement%');
        });
        
        // Filtrer par supérette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        $produits = $query->get();
        
        foreach ($produits as $produit) {
            // Récupérer l'historique des prix
            $mouvements = $produit->mouvementsStock()
                ->where('created_at', '>=', $hier)
                ->where('type', 'like', '%ajustement%')
                ->orderBy('created_at', 'asc')
                ->get();
            
            if ($mouvements->count() >= 2) {
                $premier = $mouvements->first();
                $dernier = $mouvements->last();
                
                if ($premier->prix_unitaire > 0 && $dernier->prix_unitaire > 0) {
                    $variation = abs(($dernier->prix_unitaire - $premier->prix_unitaire) / $premier->prix_unitaire * 100);
                    
                    if ($variation > 10) {
                        $anomalies[] = [
                            'type' => 'variation_prix',
                            'severite' => 'moyenne',
                            'message' => "Variation de prix importante ({$variation}%) pour le produit {$produit->nom} en moins de 24h",
                            'details' => [
                                'produit_id' => $produit->id,
                                'produit_nom' => $produit->nom,
                                'ancien_prix' => $premier->prix_unitaire,
                                'nouveau_prix' => $dernier->prix_unitaire,
                                'variation' => $variation,
                                'date_premier' => $premier->created_at,
                                'date_dernier' => $dernier->created_at
                            ]
                        ];
                    }
                }
            }
        }
    }
    
    /**
     * Détecte les séries d'annulations suspectes
     * 
     * @param array &$anomalies Tableau d'anomalies à compléter
     */
    private function detecterAnnulationsSuspectes(&$anomalies, $superetteId)
    {
        // Récupérer les annulations des dernières 24h, groupées par employé
        $hier = Carbon::now()->subDay();
        
        $query = Vente::where('statut', 'annulee')
            ->where('updated_at', '>=', $hier);
            
        // Filtrer par supérette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        $utilisateursAnnulations = $query->select('employe_id', DB::raw('count(*) as nb_annulations'), DB::raw('sum(montant_total) as montant_total'))
            ->groupBy('employe_id')
            ->having('nb_annulations', '>=', 3) // Seuil de suspicion: 3 annulations ou plus
            ->get();
        
        foreach ($utilisateursAnnulations as $ua) {
            $employe = \App\Models\Employe::find($ua->employe_id);
            $username = $employe ? "{$employe->prenom} {$employe->nom}" : "Employé #{$ua->employe_id}";
            
            // Récupérer les ventes annulées pour cet employé
            $ventesAnnulees = Vente::where('statut', 'annulee')
                ->where('updated_at', '>=', $hier)
                ->where('employe_id', $ua->employe_id)
                ->get();
            
            // Vérifier si certaines annulations sont sans motif ou avec motifs similaires
            $sansMotif = $ventesAnnulees->filter(function ($vente) {
                return empty($vente->notes);
            })->count();
            
            $severite = $ua->nb_annulations >= 5 || $sansMotif > 0 ? 'haute' : 'moyenne';
            
            $anomalies[] = [
                'type' => 'annulations_multiples',
                'severite' => $severite,
                'message' => "{$ua->nb_annulations} annulations par {$username} en 24h pour un total de " . number_format($ua->montant_total, 0, ',', ' ') . " FCFA",
                'details' => [
                    'employe_id' => $ua->employe_id,
                    'username' => $username,
                    'nb_annulations' => $ua->nb_annulations,
                    'montant_total' => $ua->montant_total,
                    'sans_motif' => $sansMotif,
                    'ventes_ids' => $ventesAnnulees->pluck('id')->toArray()
                ]
            ];
        }
    }
    
    /**
     * Détecte les ajustements de stock massifs (>20%)
     * 
     * @param array &$anomalies Tableau d'anomalies à compléter
     */
    private function detecterAjustementsStockMassifs(&$anomalies, $superetteId)
    {
        // Récupérer les ajustements de stock des dernières 24h
        $hier = Carbon::now()->subDay();
        
        $query = MouvementStock::where(function ($query) {
            $query->where('type', 'ajustement_positif')
                ->orWhere('type', 'ajustement_negatif');
        })
        ->where('created_at', '>=', $hier);
        
        // Filtrer par supérette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        $ajustements = $query->get();
        
        foreach ($ajustements as $ajustement) {
            $produit = $ajustement->produit;
            
            // Calculer le pourcentage d'ajustement par rapport au stock
            $stockAvant = $produit->stock - ($ajustement->type === 'ajustement_positif' ? $ajustement->quantite : -$ajustement->quantite);
            
            if ($stockAvant > 0) {
                $pourcentageAjustement = abs($ajustement->quantite / $stockAvant * 100);
                
                if ($pourcentageAjustement > 20) {
                    $anomalies[] = [
                        'type' => 'ajustement_stock_massif',
                        'severite' => 'moyenne',
                        'message' => "Ajustement de stock important ({$pourcentageAjustement}%) pour le produit {$produit->nom}",
                        'details' => [
                            'produit_id' => $produit->id,
                            'produit_nom' => $produit->nom,
                            'stock_avant' => $stockAvant,
                            'quantite_ajustee' => $ajustement->quantite,
                            'pourcentage' => $pourcentageAjustement,
                            'date_ajustement' => $ajustement->created_at,
                            'utilisateur' => $ajustement->utilisateur->name
                        ]
                    ];
                }
            }
        }
    }
    
    /**
     * Génère un rapport d'audit quotidien
     * 
     * @param Carbon $date Date du rapport (défaut: aujourd'hui)
     * @return array Données du rapport
     */
    public function genererRapportQuotidien($date = null)
    {
        $date = $date ?? Carbon::now();
        $debut = $date->copy()->startOfDay();
        $fin = $date->copy()->endOfDay();
        
        // Récupération des activités de la journée (filtrées par supérette active)
        $superetteId = session('active_superette_id');
        $query = \App\Models\ActivityLog::whereBetween('created_at', [$debut, $fin])
            ->orderBy('created_at', 'asc');
            
        // Si une supérette est sélectionnée, filtrer les activités par cette supérette
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }
        
        $activites = $query->get();
        
        // Récupération de toutes les ventes de la journée
        $queryToutesVentes = Vente::whereBetween(DB::raw('COALESCE(date_vente, created_at)'), [$debut, $fin]);
            
        // Filtrer par supérette si spécifiée
        if ($superetteId) {
            $queryToutesVentes->where('superette_id', $superetteId);
        }
        
        $toutesVentes = $queryToutesVentes->get();
        
        // Séparer les ventes valides et annulées
        $ventesValides = $toutesVentes->whereIn('statut', ['completee', 'terminee']);
        $ventesAnnulees = $toutesVentes->whereIn('statut', ['annulee', 'annulé']);
        
        // Récupération des paiements de la journée (avec gestion d'erreur si la table n'existe pas)
        try {
            if (Schema::hasTable('paiements')) {
                $query = Paiement::whereBetween(DB::raw('COALESCE(date_paiement, created_at)'), [$debut, $fin]);
                
                // Filtrer par supérette si spécifiée
                if ($superetteId) {
                    $query->where('superette_id', $superetteId);
                }
                
                $paiements = $query->get();
                $montantsParMethode = $paiements->groupBy('methode')
                    ->map(function ($items) {
                        return [
                            'count' => $items->count(),
                            'total' => $items->sum('montant')
                        ];
                    });
            } else {
                $paiements = collect();
                $montantsParMethode = collect();
            }
        } catch (\Exception $e) {
            $paiements = collect();
            $montantsParMethode = collect();
            \Log::warning('Erreur lors de la récupération des paiements: ' . $e->getMessage());
        }
        
        // Détection des anomalies
        $anomalies = $this->detecterAnomalies();
        
        // Calcul des ventes par heure (uniquement les ventes valides)
        $ventesParHeure = [];
        foreach ($ventesValides as $vente) {
            $heure = Carbon::parse($vente->date_vente ?? $vente->created_at)->format('H');
            if (!isset($ventesParHeure[$heure])) {
                $ventesParHeure[$heure] = ['count' => 0, 'montant' => 0];
            }
            $ventesParHeure[$heure]['count'] += 1;
            $ventesParHeure[$heure]['montant'] += $vente->montant_total;
        }
        // S'assurer que toutes les heures de 0 à 23 sont présentes
        for ($h = 0; $h < 24; $h++) {
            $key = str_pad($h, 2, '0', STR_PAD_LEFT);
            if (!isset($ventesParHeure[$key])) {
                $ventesParHeure[$key] = ['count' => 0, 'montant' => 0];
            }
        }
        ksort($ventesParHeure);
        
        // Top produits vendus (uniquement des ventes valides)
        $topVendus = [];
        if ($ventesValides->count() > 0 && Schema::hasTable('detail_ventes')) {
            $topVendus = DB::table('detail_ventes')
                ->join('produits', 'detail_ventes.produit_id', '=', 'produits.id')
                ->join('ventes', 'detail_ventes.vente_id', '=', 'ventes.id')
                ->whereBetween('ventes.created_at', [$debut, $fin])
                ->whereIn('ventes.statut', ['completee', 'terminee']) // Uniquement les ventes valides
                ->selectRaw('produits.nom, SUM(detail_ventes.quantite) as quantite, SUM(detail_ventes.sous_total) as montant')
                ->groupBy('produits.nom')
                ->orderByDesc('montant')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'nom' => $item->nom,
                        'quantite' => $item->quantite,
                        'montant' => $item->montant
                    ];
                })->toArray();
        }
        
        // Structure du rapport (toutes les clés sont garanties)
        $rapport = [
            'date' => $date->format('Y-m-d'),
            'periode' => [
                'debut' => $debut->format('Y-m-d H:i:s'),
                'fin' => $fin->format('Y-m-d H:i:s')
            ],
            'ventes' => [
                'total' => $ventesValides->count(), // Uniquement les ventes valides
                'montant_total' => $ventesValides->sum('montant_total'), // Uniquement les ventes valides
                'panier_moyen' => $ventesValides->count() > 0 ? $ventesValides->sum('montant_total') / $ventesValides->count() : 0,
                'annulees' => [
                    'total' => $ventesAnnulees->count(),
                    'montant_total' => $ventesAnnulees->sum('montant_total')
                ],
                'par_heure' => $ventesParHeure
            ],
            'paiements' => [
                'total' => $paiements->count(),
                'montant_total' => $paiements->sum('montant'),
                'par_methode' => $montantsParMethode->toArray(),
                'total_transactions' => $paiements->count(),
                'total_montant' => $paiements->sum('montant')
            ],
            'activites' => [
                'total' => $activites->count(),
                'par_type' => $activites->groupBy('type')->map->count()->toArray()
            ],
            'anomalies' => [
                'total' => count($anomalies),
                'par_severite' => collect($anomalies)->groupBy('severite')->map->count()->toArray(),
                'liste' => $anomalies
            ],
            'objectif_journalier' => config('superette.objectif_journalier', 0),
            'produits' => [
                'top_vendus' => $topVendus
            ],
            'recommandations' => []
        ];
        return $rapport;
    }
    /**
     * Génère un rapport hebdomadaire d'audit
     * 
     * @param Carbon $dateDebut Date de début de la semaine
     * @return array Données du rapport
     */
    public function genererRapportHebdomadaire($dateDebut = null)
    {
        $dateDebut = $dateDebut ?? Carbon::now()->startOfWeek();
        $dateFin = $dateDebut->copy()->addDays(6);
        
        $rapportsQuotidiens = [];
        $currentDate = $dateDebut->copy();
        
        // Générer les rapports quotidiens pour chaque jour de la semaine
        while ($currentDate <= $dateFin) {
            $rapportsQuotidiens[$currentDate->format('Y-m-d')] = $this->genererRapportQuotidien($currentDate);
            $currentDate->addDay();
        }
        
        // Agréger les données pour le rapport hebdomadaire
        $ventesTotales = 0;
        $montantTotal = 0;
        $totalAnomalies = 0;
        
        foreach ($rapportsQuotidiens as $rapport) {
            $ventesTotales += $rapport['ventes']['total'];
            $montantTotal += $rapport['ventes']['montant_total'];
            $totalAnomalies += $rapport['anomalies']['total'];
        }
        
        $panierMoyen = $ventesTotales > 0 ? $montantTotal / $ventesTotales : 0;
        
        // Structure du rapport hebdomadaire
        $rapportHebdomadaire = [
            'periode' => [
                'debut' => $dateDebut->format('Y-m-d'),
                'fin' => $dateFin->format('Y-m-d')
            ],
            'resume' => [
                'ventes_totales' => $ventesTotales,
                'montant_total' => $montantTotal,
                'panier_moyen' => $panierMoyen,
                'anomalies_totales' => $totalAnomalies
            ],
            'tendances' => [
                'ventes_par_jour' => collect($rapportsQuotidiens)->map(function ($rapport) {
                    return [
                        'date' => $rapport['date'],
                        'total' => $rapport['ventes']['total'],
                        'montant' => $rapport['ventes']['montant_total']
                    ];
                })->values()->toArray(),
                'anomalies_par_jour' => collect($rapportsQuotidiens)->map(function ($rapport) {
                    return [
                        'date' => $rapport['date'],
                        'total' => $rapport['anomalies']['total']
                    ];
                })->values()->toArray()
            ],
            'rapports_quotidiens' => $rapportsQuotidiens
        ];
        
        return $rapportHebdomadaire;
    }
    
    /**
     * Envoie une notification pour une anomalie détectée
     * 
     * @param array $anomalie Données de l'anomalie
     * @param array $destinataires Liste des emails des destinataires
     */
    public function notifierAnomalie($anomalie, $destinataires)
    {
        try {
            // Dans une implémentation réelle, on enverrait un email ou une notification push
            // Pour l'instant, on se contente de logger l'anomalie
            if (Log::getLogger()->hasHandlers() && array_key_exists('audit', config('logging.channels'))) {
                Log::channel('audit')->warning('ANOMALIE DETECTEE: ' . $anomalie['message'], $anomalie['details'] ?? []);
            } else {
                Log::warning('ANOMALIE DETECTEE: ' . $anomalie['message'], $anomalie['details'] ?? []);
            }
            
            // Stockage de l'anomalie dans la base de données si possible
            if (class_exists('\App\Models\Anomalie')) {
                \App\Models\Anomalie::create([
                    'type' => $anomalie['type'],
                    'severite' => $anomalie['severite'],
                    'message' => $anomalie['message'],
                    'details' => json_encode($anomalie['details'] ?? []),
                    'statut' => 'non_traitee'
                ]);
            }
            
            // Exemple d'envoi d'email (à décommenter et configurer)
            /*
            if (count($destinataires) > 0) {
                Mail::send('emails.anomalie', ['anomalie' => $anomalie], function ($message) use ($destinataires, $anomalie) {
                    $message->to($destinataires)
                        ->subject('[ALERTE] ' . ucfirst($anomalie['severite']) . ' - ' . $anomalie['message']);
                });
            }
            */
            
            // Alternative pour envoyer un email sans template dédié
            /*
            if (count($destinataires) > 0) {
                $sujet = '[ALERTE] ' . ucfirst($anomalie['severite']) . ' - ' . $anomalie['message'];
                $contenu = "Une anomalie a été détectée dans le système :\n\n";
                $contenu .= "Type: {$anomalie['type']}\n";
                $contenu .= "Sévérité: {$anomalie['severite']}\n";
                $contenu .= "Message: {$anomalie['message']}\n\n";
                
                if (!empty($anomalie['details'])) {
                    $contenu .= "Détails:\n";
                    foreach ($anomalie['details'] as $key => $value) {
                        $contenu .= "- {$key}: {$value}\n";
                    }
                }
                
                Mail::raw($contenu, function ($message) use ($destinataires, $sujet) {
                    $message->to($destinataires)->subject($sujet);
                });
            }
            */
        } catch (\Exception $e) {
            Log::error('Erreur lors de la notification d\'anomalie: ' . $e->getMessage());
        }
    }
    
    /**
     * Récupère les anomalies selon les filtres spécifiés
     * 
     * @param array $filtres Filtres à appliquer
     * @return Collection Collection d'anomalies
     */
    public function getAnomalies($filtres = [])
    {
        // Récupération de toutes les anomalies
        $anomalies = $this->detecterAnomalies();
        
        // Application des filtres
        if (!empty($filtres)) {
            $anomalies = collect($anomalies)->filter(function ($anomalie) use ($filtres) {
                $match = true;
                
                // Filtre par type
                if (!empty($filtres['type']) && $anomalie['type'] != $filtres['type']) {
                    $match = false;
                }
                
                // Filtre par sévérité
                if (!empty($filtres['severite']) && $anomalie['severite'] != $filtres['severite']) {
                    $match = false;
                }
                
                // Filtre par statut
                if (!empty($filtres['statut']) && (!isset($anomalie['statut']) || $anomalie['statut'] != $filtres['statut'])) {
                    $match = false;
                }
                
                // Filtre par date (à implémenter si nécessaire)
                
                // Filtre par superette_id
                if (!empty($filtres['superette_id']) && 
                    (!isset($anomalie['superette_id']) || $anomalie['superette_id'] != $filtres['superette_id'])) {
                    $match = false;
                }
                
                return $match;
            })->values()->all();
        }
        
        return $anomalies;
    }
    
    /**
     * Récupère une anomalie spécifique par son ID
     * 
     * @param int $id ID de l'anomalie
     * @return array|null Données de l'anomalie ou null si non trouvée
     */
    public function getAnomalieById($id)
    {
        $anomalies = $this->detecterAnomalies();
        
        // Pour l'instant, on retourne la première anomalie trouvée
        // Dans une implémentation réelle, les anomalies seraient stockées en base
        return $anomalies[0] ?? null;
    }
    
    /**
     * Met à jour le statut d'une anomalie
     * 
     * @param int $id ID de l'anomalie
     * @param string $statut Nouveau statut
     * @return bool
     */
    public function updateAnomalieStatus($id, $statut)
    {
        // Pour l'instant, on simule la mise à jour
        // Dans une implémentation réelle, on mettrait à jour en base
        return true;
    }
    
    /**
     * Récupère les commentaires d'une anomalie
     * 
     * @param int $id ID de l'anomalie
     * @return array
     */
    public function getCommentairesByAnomalieId($id)
    {
        // Pour l'instant, on retourne un tableau vide
        // Dans une implémentation réelle, on récupérerait depuis la base
        return [];
    }
    
    /**
     * Ajoute un commentaire à une anomalie
     * 
     * @param int $id ID de l'anomalie
     * @param string $commentaire Contenu du commentaire
     * @param int $userId ID de l'utilisateur
     * @return bool
     */
    public function addCommentToAnomalie($id, $commentaire, $userId)
    {
        // Pour l'instant, on simule l'ajout
        // Dans une implémentation réelle, on sauvegarderait en base
        return true;
    }
    
    /**
     * Récupère les anomalies avec pagination
     * 
     * @param array $filtres Filtres à appliquer
     * @param int $perPage Nombre d'éléments par page
     * @return LengthAwarePaginator Paginator d'anomalies
     */
    public function getAnomaliesPaginated($filtres = [], $perPage = 15)
    {
        $anomalies = $this->getAnomalies($filtres);
        
        // Création d'une collection pour la pagination
        $page = request()->input('page', 1);
        $offset = ($page - 1) * $perPage;
        
        // Récupération des éléments pour la page courante
        $items = array_slice($anomalies, $offset, $perPage);
        
        // Création du paginator
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            count($anomalies),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}