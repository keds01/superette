<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Produit;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function index(Request $request)
    {
        // Validation des dates
        $request->validate([
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'periode' => 'nullable|in:jour,semaine,mois,annee'
        ]);

        // Définition de la période par défaut
        $dateDebut = $request->date_debut ? Carbon::parse($request->date_debut) : Carbon::now()->startOfMonth();
        $dateFin = $request->date_fin ? Carbon::parse($request->date_fin)->endOfDay() : Carbon::now()->endOfDay();
        $periode = $request->periode ?? 'jour';
        
        // Récupération de la superette active
        $superetteId = session('active_superette_id');

        // Construction de la requête de base
        $query = Vente::query()
            ->whereBetween(DB::raw('COALESCE(date_vente, created_at)'), [$dateDebut, $dateFin])
            ->whereIn('statut', ['completee', 'terminee']);
            
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }

        // Calcul des statistiques générales avec des valeurs par défaut
        $chiffreAffaires = $query->sum('montant_total') ?? 0;
        $nombreVentes = $query->count() ?? 0;
        $panierMoyen = $nombreVentes > 0 ? $chiffreAffaires / $nombreVentes : 0;

        // Évolution des ventes selon la période
        $evolutionVentes = $this->getEvolutionVentes($dateDebut, $dateFin, $periode, $superetteId) ?? collect();

        // Répartition des modes de paiement
        $paiementsQuery = Paiement::query()
            ->valides() // Scope pour statut = 'valide'
            ->whereBetween('date_paiement', [$dateDebut, $dateFin]); // Filtrer par la date du paiement
            
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $paiementsQuery->where('superette_id', $superetteId);
        }
        
        $modesPaiement = $paiementsQuery->select('mode_paiement', DB::raw('SUM(montant) as montant')) // Assurez-vous que l'alias correspond à ce que la vue attend
            ->groupBy('mode_paiement')
            ->get();

        // Top 10 des produits les plus vendus avec une valeur par défaut
        $topProduitsQuery = DetailVente::query()
            ->join('ventes', 'detail_ventes.vente_id', '=', 'ventes.id')
            ->join('produits', 'detail_ventes.produit_id', '=', 'produits.id')
            ->join('unites', 'produits.unite_vente_id', '=', 'unites.id')
            ->join('categories', 'produits.categorie_id', '=', 'categories.id')
            ->whereBetween(DB::raw('COALESCE(ventes.date_vente, ventes.created_at)'), [$dateDebut, $dateFin])
            ->whereIn('ventes.statut', ['completee', 'terminee']);
            
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $topProduitsQuery->where('ventes.superette_id', $superetteId);
        }
        
        $topProduits = $topProduitsQuery->select(
                'produits.id',
                'produits.nom',
                'categories.nom as categorie_nom',
                'unites.nom as unite_vente',
                DB::raw('SUM(detail_ventes.quantite) as quantite_vendue'),
                DB::raw('SUM(detail_ventes.sous_total) as chiffre_affaires')
            )
            ->groupBy('produits.id', 'produits.nom', 'categories.nom', 'unites.nom')
            ->orderByDesc('quantite_vendue')
            ->limit(10)
            ->get() ?? collect();

        // Labels pour le graphique des modes de paiement
        $modePaiementLabels = $modesPaiement->pluck('mode_paiement')->toArray();

        return view('ventes.statistiques', compact(
            'chiffreAffaires',
            'nombreVentes',
            'panierMoyen',
            'evolutionVentes',
            'modesPaiement',
            'topProduits',
            'modePaiementLabels'
        ));
    }

    private function getEvolutionVentes($dateDebut, $dateFin, $periode, $superetteId = null)
    {
        $query = Vente::query()
            ->whereBetween(DB::raw('COALESCE(date_vente, created_at)'), [$dateDebut, $dateFin])
            ->whereIn('statut', ['completee', 'terminee']);
            
        // Filtrer par superette si spécifiée
        if ($superetteId) {
            $query->where('superette_id', $superetteId);
        }

        switch ($periode) {
            case 'jour':
                return $query->select(
                    DB::raw('DATE(COALESCE(date_vente, created_at)) as date'),
                    DB::raw('SUM(montant_total) as montant')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            case 'semaine':
                return $query->select(
                    DB::raw('YEARWEEK(COALESCE(date_vente, created_at)) as date'),
                    DB::raw('MIN(DATE(COALESCE(date_vente, created_at))) as date_debut'),
                    DB::raw('SUM(montant_total) as montant')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    $item->date = 'Sem. ' . Carbon::parse($item->date_debut)->format('W/Y');
                    return $item;
                });

            case 'mois':
                return $query->select(
                    DB::raw('DATE_FORMAT(COALESCE(date_vente, created_at), "%Y-%m") as date'),
                    DB::raw('SUM(montant_total) as montant')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    $item->date = Carbon::createFromFormat('Y-m', $item->date)->format('M Y');
                    return $item;
                });

            case 'annee':
                return $query->select(
                    DB::raw('YEAR(COALESCE(date_vente, created_at)) as date'),
                    DB::raw('SUM(montant_total) as montant')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    $item->date = $item->date;
                    return $item;
                });

            default:
                return collect();
        }
    }
} 