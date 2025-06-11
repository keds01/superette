<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reception extends Model
{
    use HasFactory;
    protected $fillable = [
        'numero',
        'fournisseur_id',
        'user_id',
        'date_reception',
        'numero_facture',
        'mode_paiement',
        'description',
        'statut'
    ];

    protected $casts = [
        'date_reception' => 'datetime',
    ];

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailReception::class);
    }

    public function getMontantTotalAttribute(): float
    {
        return $this->details->sum(function ($detail) {
            return $detail->quantite * $detail->prix_unitaire;
        });
    }
}
