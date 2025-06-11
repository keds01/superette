<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;
use App\Models\Categorie;

class DetailApprovisionnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'approvisionnement_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'remise',
        'total_ligne',
        'notes'
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'remise' => 'decimal:2',
        'total_ligne' => 'decimal:2'
    ];

    // Relations
    public function approvisionnement()
    {
        return $this->belongsTo(Approvisionnement::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    // Méthodes
    public function calculerTotalLigne()
    {
        $sousTotal = $this->quantite * $this->prix_unitaire;
        $remise = $sousTotal * ($this->remise / 100);
        $this->total_ligne = $sousTotal - $remise;
        $this->save();
    }

    public function getSousTotalAttribute()
    {
        return $this->quantite * $this->prix_unitaire;
    }

    public function getMontantRemiseAttribute()
    {
        return $this->sous_total * ($this->remise / 100);
    }

    // Scopes
    public function scopeParProduit($query, $produitId)
    {
        return $query->where('produit_id', $produitId);
    }

    public function scopeParFournisseur($query, $fournisseurId)
    {
        return $query->whereHas('approvisionnement', function ($q) use ($fournisseurId) {
            $q->where('fournisseur_id', $fournisseurId);
        });
    }

    public function scopeAvecRemise($query)
    {
        return $query->where('remise', '>', 0);
    }
} 