<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasSuperette;

class Employe extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory, SoftDeletes, HasSuperette;

    protected $fillable = [
        'user_id',
        'superette_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'poste',
        'statut',
        'adresse',
        'date_embauche',
        'salaire',
        'notes'
    ];

    protected $dates = [
        'date_embauche',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relation avec l'utilisateur associé
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la superette
     */
    public function superette(): BelongsTo
    {
        return $this->belongsTo(Superette::class);
    }

    /**
     * Obtenir le nom complet de l'employé
     */
    public function getNomCompletAttribute(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }
}
