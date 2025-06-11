<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    // Statuts possibles pour une vente
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINEE = 'terminee';
    const STATUT_ANNULEE = 'annulee';

    use SoftDeletes;

    protected $fillable = [
        'numero_vente',
        'client_id',
        'employe_id',
        'date_vente',
        'type_vente',
        'montant_total',
        'montant_paye',
        'montant_restant',
        'statut',
        'notes'
    ];

    protected $casts = [
        'date_vente' => 'datetime',
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_restant' => 'decimal:2',
    ];

    /**
     * Obtenir les couleurs Tailwind CSS pour le statut de la vente.
     *
     * @return array
     */
    public function getStatutColorAttribute(): array
    {
        switch (strtolower($this->statut)) {
            case 'payée':
            case 'payee': // Gérer les deux graphies pour plus de robustesse
                return ['bg' => 'bg-green-100', 'text' => 'text-green-800'];
            case 'partielle':
                return ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'];
            case 'annulée':
            case 'annulee': // Gérer les deux graphies
                return ['bg' => 'bg-red-100', 'text' => 'text-red-800'];
            case self::STATUT_EN_COURS:
                 return ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'];
            case self::STATUT_TERMINEE: // Peut-être similaire à payée ou un état distinct
                 return ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'];
            default:
                return ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
        }
    }


    // Relations
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function details()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // Événements
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vente) {
            if (empty($vente->numero_vente)) {
                $vente->numero_vente = 'V-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
            if (empty($vente->date_vente)) {
                $vente->date_vente = now();
            }
        });
    }

    /**
     * Calcule et met à jour les montants payé et restant pour la vente.
     * Cette méthode est typiquement appelée après la validation d'un paiement.
     */
    public function calculerMontants()
    {
        $montantPayeTotal = $this->paiements()->valides()->sum('montant');
        $this->montant_paye = $montantPayeTotal;
        $this->montant_restant = $this->montant_total - $this->montant_paye;

        // Assurer que le montant restant n'est pas négatif
        if ($this->montant_restant < 0) {
            $this->montant_restant = 0;
        }

        // Logique additionnelle pour le statut de la vente si nécessaire
        // Exemple : si ($this->montant_restant <= 0) { $this->statut = 'payee'; }
        // Pour l'instant, on se contente de mettre à jour les montants.

        $this->save();
    }
    
    /**
     * Détermine si la vente peut être modifiée.
     * Une vente ne peut être modifiée que si elle est en cours.
     *
     * @return bool
     */
    public function peutEtreModifiee(): bool
    {
        return $this->statut === self::STATUT_EN_COURS;
    }
    
    /**
     * Détermine si la vente peut être annulée.
     * Une vente peut être annulée si elle n'est pas déjà annulée.
     *
     * @return bool
     */
    public function peutEtreAnnulee(): bool
    {
        return $this->statut !== 'annulee' && $this->statut !== self::STATUT_ANNULEE;
    }
    
    /**
     * Détermine si la vente peut recevoir un paiement.
     * Une vente peut être payée si elle est terminée et que le montant restant est supérieur à zéro.
     *
     * @return bool
     */
    public function peutEtrePayee(): bool
    {
        return $this->statut === self::STATUT_TERMINEE && $this->montant_restant > 0;
    }
}
