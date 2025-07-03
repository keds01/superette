<?php

namespace App\Models;

use App\Traits\HasSuperette;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory, HasSuperette;

    protected $fillable = [
        'produit_id',
        'type',
        'valeur',
        'date_debut',
        'date_fin',
        'description',
        'actif',
        'superette_id'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'actif' => 'boolean',
        'valeur' => 'decimal:2'
    ];

    // Relations
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('actif', true)
                    ->where('date_debut', '<=', now())
                    ->where('date_fin', '>=', now());
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('produit_id', $productId);
    }

    // Méthodes
    public function estValide()
    {
        return $this->actif && 
               $this->date_debut <= now() && 
               $this->date_fin >= now();
    }

    public function calculerPrixPromo($prixOriginal)
    {
        if (!$this->estValide()) {
            return $prixOriginal;
        }

        if ($this->type === 'pourcentage') {
            return $prixOriginal * (1 - $this->valeur / 100);
        }

        return max(0, $prixOriginal - $this->valeur);
    }
}
