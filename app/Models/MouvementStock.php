<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'produit_id',
        'type',
        'quantite_avant_conditionnement',
        'quantite_avant_unite',
        'quantite_apres_conditionnement',
        'quantite_apres_unite',
        'date_peremption',
        'reference_mouvement',
        'type_reference',
        'motif',
        'user_id',
        'date_mouvement'
    ];

    protected $casts = [
        'date_peremption' => 'date',
        'date_mouvement' => 'datetime',
        'quantite_avant_conditionnement' => 'float',
        'quantite_avant_unite' => 'float',
        'quantite_apres_conditionnement' => 'float',
        'quantite_apres_unite' => 'float'
    ];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
} 