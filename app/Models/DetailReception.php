<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailReception extends Model
{
    use HasFactory;
    protected $fillable = [
        'reception_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'date_peremption'
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'date_peremption' => 'date'
    ];

    public function reception(): BelongsTo
    {
        return $this->belongsTo(Reception::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function getMontantTotalAttribute(): float
    {
        return $this->quantite * $this->prix_unitaire;
    }
}
