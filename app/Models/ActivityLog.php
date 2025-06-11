<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'user_id',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    /**
     * Utilisateur qui a effectué l'action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère le modèle concerné par l'activité
     */
    public function model()
    {
        return $this->morphTo();
    }
}
