<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commande extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero_commande',
        'fournisseur_id',
        'date_commande',
        'date_livraison_prevue',
        'statut',
        'montant_total',
        'devise',
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'date_livraison_prevue' => 'datetime',
        'montant_total' => 'decimal:2',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function details()
    {
        return $this->hasMany(DetailCommande::class);
    }

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'detail_commandes')
            ->withPivot(['quantite', 'prix_unitaire', 'montant_total'])
            ->withTimestamps();
    }
}
