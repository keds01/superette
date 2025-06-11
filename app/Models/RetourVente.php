<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RetourVente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'retours_ventes';

    protected $fillable = [
        'vente_id',
        'employe_id',
        'montant_total',
        'statut',
        'motif_retour',
        'notes',
        'date_retour'
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'date_retour' => 'datetime'
    ];

    // Constantes pour les statuts
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINE = 'termine';
    const STATUT_ANNULE = 'annule';

    /**
     * Relations
     */
    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function details()
    {
        return $this->hasMany(DetailRetour::class);
    }

    /**
     * Scopes pour les requêtes
     */
    public function scopeEnCours($query)
    {
        return $query->where('statut', self::STATUT_EN_COURS);
    }

    public function scopeTermines($query)
    {
        return $query->where('statut', self::STATUT_TERMINE);
    }

    public function scopeAnnules($query)
    {
        return $query->where('statut', self::STATUT_ANNULE);
    }

    public function scopeParPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_retour', [$debut, $fin]);
    }

    public function scopeParEmploye($query, $employeId)
    {
        return $query->where('employe_id', $employeId);
    }

    /**
     * Méthodes utilitaires
     */
    public function calculerMontantTotal()
    {
        $this->montant_total = $this->details->sum('montant_total');
        $this->save();
    }

    public function terminer()
    {
        if ($this->statut !== self::STATUT_EN_COURS) {
            throw new \Exception('Seul un retour en cours peut être terminé.');
        }

        $this->statut = self::STATUT_TERMINE;
        $this->save();

        // Mettre à jour le statut de la vente si nécessaire
        if ($this->vente->statut === Vente::STATUT_TERMINEE) {
            $this->vente->statut = Vente::STATUT_REMBOURSEE;
            $this->vente->save();
        }
    }

    public function annuler()
    {
        if ($this->statut !== self::STATUT_EN_COURS) {
            throw new \Exception('Seul un retour en cours peut être annulé.');
        }

        $this->statut = self::STATUT_ANNULE;
        $this->save();

        // Restaurer le stock pour chaque détail
        foreach ($this->details as $detail) {
            $detail->produit->decrement('quantite', $detail->quantite_retournee);
        }
    }

    public function peutEtreTermine()
    {
        return $this->statut === self::STATUT_EN_COURS;
    }

    public function peutEtreAnnule()
    {
        return $this->statut === self::STATUT_EN_COURS;
    }

    /**
     * Événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($retour) {
            if (empty($retour->date_retour)) {
                $retour->date_retour = now();
            }
        });

        static::created(function ($retour) {
            $retour->calculerMontantTotal();
        });

        static::updated(function ($retour) {
            $retour->calculerMontantTotal();
        });
    }
} 