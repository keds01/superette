<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'parent_id',
        'actif',
        'system' // Ajouté pour permettre la gestion via Eloquent
    ];

    protected $casts = [
        'actif' => 'boolean'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Produit::class, 'categorie_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alerte::class, 'categorie_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeRecherche($query, $term)
    {
        return $query->where('nom', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%");
    }
} 