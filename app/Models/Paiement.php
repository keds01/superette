<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paiements';

    protected $fillable = [
        'vente_id',
        'mode_paiement',
        'montant',
        'reference_paiement',
        'statut',
        'notes',
        'date_paiement'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'datetime'
    ];

    // Constantes pour les modes de paiement
    const MODE_ESPECES = 'especes';
    const MODE_CARTE = 'carte';
    const MODE_CHEQUE = 'cheque';
    const MODE_MOBILE = 'mobile';
    const MODE_VIREMENT = 'virement';

    // Constantes pour les statuts
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_VALIDE = 'valide';
    const STATUT_REFUSE = 'refuse';
    const STATUT_REMBOURSE = 'rembourse';

    /**
     * Relations
     */
    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    /**
     * Scopes pour les requêtes
     */
    public function scopeValides($query)
    {
        return $query->where('statut', self::STATUT_VALIDE);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }

    public function scopeRefuses($query)
    {
        return $query->where('statut', self::STATUT_REFUSE);
    }

    public function scopeRembourses($query)
    {
        return $query->where('statut', self::STATUT_REMBOURSE);
    }

    public function scopeParPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_paiement', [$debut, $fin]);
    }

    public function scopeParMode($query, $mode)
    {
        return $query->where('mode_paiement', $mode);
    }

    /**
     * Méthodes utilitaires
     */
    public function valider()
    {
        if ($this->statut !== self::STATUT_EN_ATTENTE) {
            throw new \Exception('Seul un paiement en attente peut être validé.');
        }

        $this->statut = self::STATUT_VALIDE;
        $this->save();

        // Recalculer les montants de la vente
        $this->vente->calculerMontants();
    }

    public function refuser()
    {
        if ($this->statut !== self::STATUT_EN_ATTENTE) {
            throw new \Exception('Seul un paiement en attente peut être refusé.');
        }

        $this->statut = self::STATUT_REFUSE;
        $this->save();

        // Recalculer les montants de la vente
        $this->vente->calculerMontants();
    }

    public function rembourser()
    {
        if ($this->statut !== self::STATUT_VALIDE) {
            throw new \Exception('Seul un paiement validé peut être remboursé.');
        }

        $this->statut = self::STATUT_REMBOURSE;
        $this->save();

        // Recalculer les montants de la vente
        $this->vente->calculerMontants();
    }

    public function peutEtreValide()
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    public function peutEtreRefuse()
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    public function peutEtreRembourse()
    {
        return $this->statut === self::STATUT_VALIDE;
    }

    /**
     * Événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($paiement) {
            if (empty($paiement->date_paiement)) {
                $paiement->date_paiement = now();
            }
        });

        static::created(function ($paiement) {
            $paiement->vente->calculerMontants();
        });

        static::updated(function ($paiement) {
            $paiement->vente->calculerMontants();
        });

        static::deleted(function ($paiement) {
            $paiement->vente->calculerMontants();
        });
    }
} 