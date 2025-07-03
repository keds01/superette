<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentEmploye extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents_employes';

    protected $fillable = [
        'employe_id',
        'type',
        'titre',
        'fichier',
        'date_document',
        'date_expiration',
        'est_confidentiel',
        'description'
    ];

    protected $casts = [
        'date_document' => 'date',
        'date_expiration' => 'date',
        'est_confidentiel' => 'boolean'
    ];

    // Constantes pour les types de documents
    const TYPE_CV = 'cv';
    const TYPE_CONTRAT = 'contrat';
    const TYPE_DIPLOME = 'diplome';
    const TYPE_CERTIFICAT = 'certificat';
    const TYPE_AUTRE = 'autre';

    // Relation avec l'employé
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Scopes pour filtrer les documents
    public function scopeConfidentiels($query)
    {
        return $query->where('est_confidentiel', true);
    }

    public function scopeNonConfidentiels($query)
    {
        return $query->where('est_confidentiel', false);
    }

    public function scopeExpires($query)
    {
        return $query->whereNotNull('date_expiration')
                    ->where('date_expiration', '<', now());
    }

    public function scopeExpireBientot($query, $jours = 30)
    {
        return $query->whereNotNull('date_expiration')
                    ->where('date_expiration', '>', now())
                    ->where('date_expiration', '<=', now()->addDays($jours));
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Méthodes utilitaires
    public function estExpire()
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }

    public function joursAvantExpiration()
    {
        if (!$this->date_expiration) {
            return null;
        }
        return now()->diffInDays($this->date_expiration, false);
    }

    public function getTypeLibelleAttribute()
    {
        return match($this->type) {
            self::TYPE_CV => 'CV',
            self::TYPE_CONTRAT => 'Contrat',
            self::TYPE_DIPLOME => 'Diplôme',
            self::TYPE_CERTIFICAT => 'Certificat',
            self::TYPE_AUTRE => 'Autre',
            default => 'Inconnu'
        };
    }

    public function getCheminFichierAttribute()
    {
        return storage_path('app/documents/employes/' . $this->fichier);
    }

    public function getUrlFichierAttribute()
    {
        return route('documents.employes.show', $this->id);
    }

    // Méthode pour vérifier si l'utilisateur a accès au document
    public function utilisateurPeutAcceder($user)
    {
        // Si le document n'est pas confidentiel, tout le monde peut y accéder
        if (!$this->est_confidentiel) {
            return true;
        }

        // Vérifier si l'utilisateur est l'employé concerné
        if ($user->id === $this->employe_id) {
            return true;
        }

        // Vérifier si l'utilisateur a les permissions nécessaires
        return $user->hasRole(['admin', 'super_admin', 'rh', 'manager']);
    }
} 