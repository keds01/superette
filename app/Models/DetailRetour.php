<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;
use App\Models\Categorie;

class DetailRetour extends Model
{
    use HasFactory;

    protected $table = 'details_retours';

    protected $fillable = [
        'retour_vente_id',
        'produit_id',
        'quantite_retournee',
        'prix_unitaire',
        'montant_total',
        'raison_retour'
    ];

    protected $casts = [
        'quantite_retournee' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2'
    ];

    /**
     * Relations
     */
    public function retourVente()
    {
        return $this->belongsTo(RetourVente::class, 'retour_vente_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    /**
     * Méthodes utilitaires
     */
    public function calculerMontantTotal()
    {
        $this->montant_total = $this->quantite_retournee * $this->prix_unitaire;
        $this->save();
    }

    /**
     * Événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($detail) {
            // Incrémenter le stock
            $detail->produit->increment('stock', $detail->quantite_retournee);
            // Recalculer le montant total du retour
            $detail->retourVente->calculerMontantTotal();
        });

        static::updated(function ($detail) {
            // Ajuster le stock si la quantité a changé
            if ($detail->isDirty('quantite_retournee')) {
                $difference = $detail->quantite_retournee - $detail->getOriginal('quantite_retournee');
                $detail->produit->increment('stock', $difference);
            }
            // Recalculer le montant total du retour
            $detail->retourVente->calculerMontantTotal();
        });

        static::deleted(function ($detail) {
            // Décrémenter le stock
            $detail->produit->decrement('stock', $detail->quantite_retournee);
            // Recalculer le montant total du retour
            $detail->retourVente->calculerMontantTotal();
        });
    }
} 