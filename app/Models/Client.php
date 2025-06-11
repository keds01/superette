<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'code',
        'nom',
        'prenom',
        'telephone',
        'email',
        'adresse',
        'notes',
        'statut',
        'type',  // Ajoutez également ce champ qui est requis mais manquant
    ];

    /**
     * Obtenir toutes les ventes du client
     */
    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    /**
     * Obtenir tous les paiements du client
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }
}