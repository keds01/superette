<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'fournisseur_id',
        'quantite',
        'prix_unitaire',
        'date_reception',
        'date_peremption',
        'numero_lot',
        'type_mouvement',
        'reference_mouvement',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'date_reception' => 'date',
        'date_peremption' => 'date',
        'type_mouvement' => 'string'
    ];

    // Relations
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'product_id');
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeEntrees($query)
    {
        return $query->where('type_mouvement', 'entree');
    }

    public function scopeSorties($query)
    {
        return $query->where('type_mouvement', 'sortie');
    }

    public function scopeAjustements($query)
    {
        return $query->where('type_mouvement', 'ajustement');
    }

    public function scopeProchePeremption($query, $jours = 30)
    {
        return $query->whereNotNull('date_peremption')
            ->where('date_peremption', '<=', now()->addDays($jours));
    }

    public function scopeParPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_reception', [$debut, $fin]);
    }

    // Accesseurs
    public function getMontantTotalAttribute()
    {
        return $this->quantite * $this->prix_unitaire;
    }

    public function getEstPerimeAttribute()
    {
        if (!$this->date_peremption) return false;
        return $this->date_peremption->isPast();
    }

    public function getJoursAvantPeremptionAttribute()
    {
        if (!$this->date_peremption) return null;
        return now()->diffInDays($this->date_peremption, false);
    }
}
