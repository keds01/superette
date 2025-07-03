<?php

namespace App\Models;

use App\Traits\HasSuperette;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerte extends Model
{
    use HasFactory, HasSuperette;
    
    protected $table = 'alertes';

    protected $fillable = [
        'produit_id',
        'type',
        'seuil',
        'periode',
        'message',
        'estDeclenchee',
        'actif',
        'notification_email',
        'date_peremption',
        'date_resolution',
        'superette_id'
    ];

    protected $casts = [
        'estDeclenchee' => 'boolean',
        'seuil' => 'float',
        'periode' => 'integer',
        'actif' => 'boolean',
        'date_resolution' => 'datetime',
        'date_peremption' => 'date'
    ];

    /**
     * Relation avec le produit
     */
    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    /**
     * Relation avec la catégorie
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }
    
    /**
     * Scope pour filtrer les alertes actives
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }
    
    /**
     * Scope pour filtrer par type d'alerte
     */
    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Vérifie si l'alerte est déclenchée
     */
    public function estDeclenchee(): bool
    {
        if (!$this->produit) {
            return false;
        }
        if ($this->type === 'seuil_minimum') {
            return $this->produit->stock_actuel <= $this->seuil;
        } elseif ($this->type === 'seuil_maximum') {
            return $this->produit->stock_actuel >= $this->seuil;
        }
        return false;
    }

    /**
     * Retourne le message de l'alerte
     */
    public function getMessageAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $produit = $this->produit->nom;
        if ($this->type === 'seuil_minimum') {
            return "Le stock de {$produit} est inférieur au seuil minimum ({$this->seuil} unités)";
        } else {
            return "Le stock de {$produit} dépasse le seuil maximum ({$this->seuil} unités)";
        }
    }
}