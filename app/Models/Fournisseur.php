<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'code',
        'contact_principal',
        'telephone',
        'email',
        'adresse',
        'ville',
        'pays',
        'ninea',
        'registre_commerce',
        'notes',
        'solde_actuel',
        'statut',
        'actif'
    ];

    protected $casts = [
        'solde_actuel' => 'decimal:2',
        'statut' => 'string',
        'actif' => 'boolean'
    ];

    // Relations
    public function contacts()
    {
        return $this->hasMany(ContactFournisseur::class);
    }

    public function paiements()
    {
        return $this->hasMany(PaiementFournisseur::class);
    }

    public function evaluations()
    {
        return $this->hasMany(EvaluationFournisseur::class);
    }

    public function approvisionnements()
    {
        return $this->hasMany(Approvisionnement::class);
    }

    public function factures()
    {
        return $this->hasMany(FactureFournisseur::class);
    }

    // Méthodes
    public function getSoldeTotalAttribute()
    {
        return $this->factures()
            ->where('statut', '!=', 'annulee')
            ->sum('montant_restant');
    }

    public function getNoteMoyenneAttribute()
    {
        $evaluations = $this->evaluations;
        if ($evaluations->isEmpty()) {
            return null;
        }

        $total = $evaluations->sum(function ($evaluation) {
            return ($evaluation->qualite_produits +
                    $evaluation->delai_livraison +
                    $evaluation->prix_competitifs +
                    $evaluation->service_client) / 4;
        });

        return round($total / $evaluations->count(), 2);
    }

    public function getStatutEvaluationAttribute()
    {
        $note = $this->note_moyenne;
        if ($note === null) {
            return 'non_evalue';
        }

        return match(true) {
            $note >= 8 => 'excellent',
            $note >= 6 => 'bon',
            $note >= 4 => 'moyen',
            default => 'insuffisant'
        };
    }

    public function getNombreCommandesAttribute()
    {
        return $this->approvisionnements()->count();
    }

    public function getMontantTotalCommandesAttribute()
    {
        return $this->approvisionnements()
            ->where('statut', '!=', 'annulee')
            ->sum('montant_total');
    }

    public function getDelaiMoyenLivraisonAttribute()
    {
        $approvisionnements = $this->approvisionnements()
            ->whereNotNull('date_livraison_reelle')
            ->where('statut', '!=', 'annulee')
            ->get();

        if ($approvisionnements->isEmpty()) {
            return null;
        }

        $totalJours = $approvisionnements->sum(function ($appro) {
            return $appro->date_livraison_reelle->diffInDays($appro->date_commande);
        });

        return round($totalJours / $approvisionnements->count(), 1);
    }

    // Scopes
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    public function scopeInactifs($query)
    {
        return $query->where('actif', false);
    }

    public function scopeAvecSolde($query)
    {
        return $query->where('solde_actuel', '>', 0);
    }

    public function scopeRecherche($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nom', 'like', "%{$term}%")
              ->orWhere('code', 'like', "%{$term}%")
              ->orWhere('telephone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('ninea', 'like', "%{$term}%");
        })->orWhereHas('contacts', function ($q) use ($term) {
            $q->where('nom', 'like', "%{$term}%")
              ->orWhere('prenom', 'like', "%{$term}%");
        });
    }
}
