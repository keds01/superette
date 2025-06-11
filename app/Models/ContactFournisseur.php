<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactFournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fournisseur_id',
        'nom',
        'prenom',
        'fonction',
        'telephone',
        'email',
        'est_principal',
        'notes'
    ];

    protected $casts = [
        'est_principal' => 'boolean'
    ];

    // Relations
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    // Méthodes
    public function getNomCompletAttribute()
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function definirCommePrincipal()
    {
        // Désactiver le statut principal pour tous les autres contacts
        $this->fournisseur->contacts()
            ->where('id', '!=', $this->id)
            ->update(['est_principal' => false]);

        // Définir ce contact comme principal
        $this->est_principal = true;
        $this->save();
    }

    // Scopes
    public function scopePrincipaux($query)
    {
        return $query->where('est_principal', true);
    }

    public function scopeRecherche($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nom', 'like', "%{$term}%")
              ->orWhere('prenom', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('telephone', 'like', "%{$term}%")
              ->orWhere('fonction', 'like', "%{$term}%");
        });
    }
} 