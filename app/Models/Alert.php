<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;

    protected $table = 'alertes';

    protected $fillable = [
        'type',
        'message',
        'seuil',
        'periode',
        'categorie_id',
        'actif'
    ];

    protected $casts = [
        'seuil' => 'decimal:2',
        'periode' => 'integer',
        'actif' => 'boolean'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }
}