<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remise extends Model
{
    use HasFactory;

    protected $table = 'remises';

    protected $fillable = [
        'vente_id',
        'code_remise',
        'type_remise',
        'valeur_remise',
        'montant_remise',
        'description'
    ];

    protected $casts = [
        'valeur_remise' => 'decimal:2',
        'montant_remise' => 'decimal:2'
    ];

    // Constantes pour les types de remise
    const TYPE_POURCENTAGE = 'pourcentage';
    const TYPE_MONTANT_FIXE = 'montant_fixe';

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
    public function scopeParType($query, $type)
    {
        return $query->where('type_remise', $type);
    }

    public function scopeParCode($query, $code)
    {
        return $query->where('code_remise', $code);
    }

    /**
     * Méthodes utilitaires
     */
    public function calculerMontantRemise($montantTotal)
    {
        if ($this->type_remise === self::TYPE_POURCENTAGE) {
            $this->montant_remise = ($montantTotal * $this->valeur_remise) / 100;
        } else {
            $this->montant_remise = min($this->valeur_remise, $montantTotal);
        }
        $this->save();
    }

    public function estPourcentage()
    {
        return $this->type_remise === self::TYPE_POURCENTAGE;
    }

    public function estMontantFixe()
    {
        return $this->type_remise === self::TYPE_MONTANT_FIXE;
    }

    /**
     * Événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($remise) {
            $remise->vente->calculerMontants();
        });

        static::updated(function ($remise) {
            $remise->vente->calculerMontants();
        });

        static::deleted(function ($remise) {
            $remise->vente->calculerMontants();
        });
    }
} 