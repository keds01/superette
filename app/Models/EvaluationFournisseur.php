<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationFournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fournisseur_id',
        'user_id',
        'date_evaluation',
        'qualite_produits',
        'delai_livraison',
        'prix_competitifs',
        'service_client',
        'commentaire',
        'recommandation'
    ];

    protected $casts = [
        'date_evaluation' => 'datetime',
        'qualite_produits' => 'integer',
        'delai_livraison' => 'integer',
        'prix_competitifs' => 'integer',
        'service_client' => 'integer'
    ];

    // Relations
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes
    public function getNoteMoyenneAttribute()
    {
        return round(($this->qualite_produits +
                      $this->delai_livraison +
                      $this->prix_competitifs +
                      $this->service_client) / 4, 2);
    }

    public function getStatutEvaluationAttribute()
    {
        $note = $this->note_moyenne;
        return match(true) {
            $note >= 8 => 'excellent',
            $note >= 6 => 'bon',
            $note >= 4 => 'moyen',
            default => 'insuffisant'
        };
    }

    public function getCouleurStatutAttribute()
    {
        return match($this->statut_evaluation) {
            'excellent' => 'success',
            'bon' => 'info',
            'moyen' => 'warning',
            'insuffisant' => 'danger',
            default => 'secondary'
        };
    }

    public function getTexteStatutAttribute()
    {
        return match($this->statut_evaluation) {
            'excellent' => 'Excellent',
            'bon' => 'Bon',
            'moyen' => 'Moyen',
            'insuffisant' => 'Insuffisant',
            default => 'Non évalué'
        };
    }

    // Scopes
    public function scopeParPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_evaluation', [$debut, $fin]);
    }

    public function scopeParNote($query, $noteMin, $noteMax)
    {
        return $query->whereRaw('(qualite_produits + delai_livraison + prix_competitifs + service_client) / 4 BETWEEN ? AND ?', [$noteMin, $noteMax]);
    }

    public function scopeParStatut($query, $statut)
    {
        return $query->where(function ($q) use ($statut) {
            $noteMin = match($statut) {
                'excellent' => 8,
                'bon' => 6,
                'moyen' => 4,
                'insuffisant' => 0,
                default => 0
            };
            $noteMax = match($statut) {
                'excellent' => 10,
                'bon' => 7.99,
                'moyen' => 5.99,
                'insuffisant' => 3.99,
                default => 10
            };
            $q->whereRaw('(qualite_produits + delai_livraison + prix_competitifs + service_client) / 4 BETWEEN ? AND ?', [$noteMin, $noteMax]);
        });
    }

    public function scopeParUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
} 