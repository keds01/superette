<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unites';

    protected $fillable = [
        'nom',
        'symbole',
        'description',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    // Relation avec les produits
    public function produits()
    {
        return $this->hasMany(Produit::class, 'unite_vente_id');
    }

    // Récupérer toutes les unités actives
    public function scopeActives($query)
    {
        return $query->where('actif', true);
    }
} 