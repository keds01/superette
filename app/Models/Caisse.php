<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caisse extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero',
        'nom',
        'solde',
        'is_active',
        'description',
        'localisation',
        'user_id',
        // Champs pour enregistrer les opérations
        'type_operation',
        'montant',
        'mode_paiement',
        'notes_operation',
        'vente_id'
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec les ventes
     */
    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifie si la caisse a des ventes en cours
     */
    public function hasVentesEnCours(): bool
    {
        return $this->ventes()
            ->where('statut', 'en_cours')
            ->exists();
    }

    /**
     * Vérifie si la caisse peut effectuer une opération
     */
    public function canPerformOperation(float $montant, string $type = 'sortie'): bool
    {
        if ($type === 'sortie') {
            return $this->solde >= $montant;
        }
        return true;
    }

    /**
     * Génère un numéro d'opération de caisse unique au format CAISSE-AAAAMMJJ-XXX
     *
     * @return string Numéro d'opération unique au format CAISSE-AAAAMMJJ-XXX
     */
    public static function genererNumeroOperation()
    {
        // Format de la date: AAAAMMJJ (ex: 20250526)
        $date = now()->format('Ymd');

        // Recherche de la dernière opération du jour pour obtenir le dernier numéro
        $lastOperation = self::where('numero', 'like', "CAISSE-{$date}-%")
            ->orderBy('numero', 'desc')
            ->first();

        if ($lastOperation) {
            // Extraction du numéro séquentiel (les 3 derniers caractères) et incrémentation
            $lastNumber = (int) substr($lastOperation->numero, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // Première opération du jour
            $newNumber = '001';
        }

        // Construction du numéro d'opération complet
        return "CAISSE-{$date}-{$newNumber}";
    }

    /**
     * Scope pour filtrer les opérations de caisse par période (entre deux dates)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \DateTime|string $debut Date de début de la période
     * @param \DateTime|string $fin Date de fin de la période
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParPeriode($query, $debut, $fin)
    {
        // Assurez-vous que la table `mouvements_caisses` a une colonne de date, par exemple `created_at`
        // Cette implémentation filtre sur la date de création des mouvements liés à la caisse.
        // Si la date de l'opération est stockée ailleurs, ajustez la condition WHERE.
        return $query->whereHas('mouvements', function ($q) use ($debut, $fin) {
            $q->whereBetween('created_at', [$debut, $fin]);
        });
    }
}
