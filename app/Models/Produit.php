<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Scopes\SuperetteScope;

class Produit extends Model
{
    /**
     * Valeur par défaut du délai d'alerte avant péremption (en jours)
     */
    const DELAI_ALERTE_PEREMPTION_DEFAUT = 30;
    
    use HasFactory, SoftDeletes;

    protected $table = 'produits';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new SuperetteScope);
    }

    protected $fillable = [
        'nom',
        'reference',
        'code_barres',
        'description',
        'categorie_id',
        'fournisseur_id',
        'marque_id',
        'unite_mesure_id',
        'unite_vente_id',
        'conditionnement',
        'stock',
        'seuil_alerte',
        'emplacement_rayon',
        'emplacement_etagere',
        'image',
        'date_peremption',
        'delai_alerte_peremption',
        'conditionnement_fournisseur',
        'quantite_par_conditionnement',
        'prix_achat_ht',
        'prix_vente_ht',
        'prix_vente_ttc',
        'marge',
        'tva',
        'actif',
        'superette_id'
    ];

    protected $casts = [
        'stock' => 'decimal:2',
        'seuil_alerte' => 'decimal:2',
        'quantite_par_conditionnement' => 'decimal:2',
        'prix_achat_ht' => 'decimal:2',
        'prix_vente_ht' => 'decimal:2',
        'prix_vente_ttc' => 'decimal:2',
        'marge' => 'decimal:2',
        'tva' => 'decimal:2',
        'date_peremption' => 'date',
        'delai_alerte_peremption' => 'integer',
        'actif' => 'boolean'
    ];

    // Always include virtual attributes in JSON serialization
    protected $appends = ['code_barre'];

    // Relations
    /**
     * Relation vers le fournisseur du produit
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function uniteVente()
    {
        return $this->belongsTo(Unite::class, 'unite_vente_id');
    }

    public function superette()
    {
        return $this->belongsTo(Superette::class, 'superette_id');
    }

    public function mouvementsStock()
    {
        return $this->hasMany(MouvementStock::class, 'produit_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function detailsVente(): HasMany
    {
        return $this->hasMany(DetailVente::class, 'produit_id');
    }

    public function detailsApprovisionnement(): HasMany
    {
        return $this->hasMany(DetailApprovisionnement::class, 'produit_id');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class, 'produit_id');
    }

    public function conditionnements()
    {
        return $this->hasMany(Conditionnement::class);
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeEnAlerte($query)
    {
        return $query->where('stock', '<=', DB::raw('seuil_alerte'));
    }

    public function scopeParCategorie($query, $categorieId)
    {
        return $query->where('categorie_id', $categorieId);
    }

    public function scopeRecherche($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('nom', 'like', "%{$term}%")
              ->orWhere('code_barres', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // Accesseurs
    public function getStockActuelAttribute()
    {
        // Si un jour on gère les mouvements, remplacer par un calcul dynamique
        return $this->stock;
    }

    public function getMargePourcentageAttribute()
    {
        // Retourne le pourcentage de marge calculé sur la base de la marge brute et du prix d'achat HT
        if ($this->prix_achat_ht == 0) {
            return 0;
        }
        return ($this->marge_brute / $this->prix_achat_ht) * 100;
    }

    public function getPromotionActiveAttribute()
    {
        return $this->promotions()
            ->where('actif', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->first();
    }

    public function getPrixPromoAttribute()
    {
        $promotion = $this->promotion_active;
        if (!$promotion) {
            return $this->prix_vente_ttc;
        }

        return $promotion->calculerPrixPromo($this->prix_vente_ttc);
    }

    public function getPrixVenteTTCAttribute(): float
    {
        // Si le prix est déjà défini dans la base de données, l'utiliser
        if (isset($this->attributes['prix_vente_ttc']) && $this->attributes['prix_vente_ttc'] > 0) {
            return (float)$this->attributes['prix_vente_ttc'];
        }
        
        // Sinon calculer à partir du prix HT
        return $this->prix_vente_ht * (1 + $this->tva / 100);
    }

    public function getPrixAchatTTCAttribute(): float
    {
        return $this->prix_achat_ht * (1 + $this->tva / 100);
    }

    public function getMargeBruteAttribute(): float
    {
        return $this->prix_vente_ht - $this->prix_achat_ht;
    }

    public function getValeurTvaAttribute(): float
    {
        // 'tva' est stocké comme un pourcentage, ex: 18 pour 18%
        // 'prix_vente_ht' est le prix avant taxe
        if ($this->prix_vente_ht === null || $this->tva === null) {
            return 0.0;
        }
        return round(((float)$this->prix_vente_ht * ((float)$this->tva / 100)), 2);
    }

    public function getMargeBrutePourcentageAttribute(): float
    {
        if ($this->prix_achat_ht == 0) {
            return 0;
        }
        return ($this->marge_brute / $this->prix_achat_ht) * 100;
    }

    public function getValeurStockAttribute(): float
    {
        return $this->stock * $this->prix_achat_ht;
    }

    public function getEmplacementCompletAttribute()
    {
        return "{$this->emplacement_rayon} - {$this->emplacement_etagere}";
    }

    public function getStockTotalAttribute()
    {
        return $this->stock * $this->quantite_par_conditionnement;
    }

    /**
     * Alias pour accéder au champ code_barres sous le nom code_barre (singulier)
     * Cela permet d'utiliser $produit->code_barre côté front sans modifier la colonne de base de données.
     *
     * @return string|null
     */
    public function getCodeBarreAttribute()
    {
        return $this->code_barres;
    }

    // Méthodes utilitaires
    public function calculerPrixVenteHT()
    {
        return $this->prix_achat_ht * (1 + $this->marge / 100);
    }

    public function calculerPrixVenteTTC()
    {
        return $this->calculerPrixVenteHT() * (1 + $this->tva / 100);
    }

    public function estEnAlerteStock()
    {
        return $this->stock <= $this->seuil_alerte;
    }

    /**
     * Vérifie si le produit est proche de sa date de péremption
     * Utilise le délai personnalisé du produit s'il existe, sinon utilise la valeur par défaut de 30 jours
     * 
     * @return boolean
     */
    public function estProchePeremption()
    {
        if (!$this->date_peremption) {
            return false;
        }
        // Utiliser le délai personnalisé (s'il existe) ou la valeur par défaut (30 jours)
        $delai = $this->delai_alerte_peremption ?? 30;
        return now()->diffInDays($this->date_peremption, false) <= $delai && now()->diffInDays($this->date_peremption, false) > 0;
    }

    /**
     * Calcule la répartition optimale des conditionnements et le prix total pour une quantité donnée
     * @param int $quantite
     * @return array [ 'detail' => [ [type, quantite, prix_unitaire, total] ], 'total' => float ]
     */
    public function calculTarifOptimal($quantite)
    {
        $conditionnements = $this->conditionnements()->orderByDesc('quantite')->get();
        $reste = $quantite;
        $detail = [];
        $total = 0;
        foreach ($conditionnements as $cond) {
            if ($reste >= $cond->quantite) {
                $nb = intdiv($reste, $cond->quantite);
                $total += $nb * $cond->prix;
                $detail[] = [
                    'type' => $cond->type,
                    'quantite' => $nb * $cond->quantite,
                    'conditionnement' => $cond->quantite,
                    'nb_conditionnements' => $nb,
                    'prix_unitaire' => $cond->prix,
                    'total' => $nb * $cond->prix
                ];
                $reste = $reste % $cond->quantite;
            }
        }
        // Si reste, chercher le plus petit conditionnement dispo
        if ($reste > 0 && $conditionnements->count()) {
            $cond = $conditionnements->where('quantite', 1)->first() ?? $conditionnements->last();
            $total += $reste * ($cond->prix / $cond->quantite);
            $detail[] = [
                'type' => $cond->type,
                'quantite' => $reste,
                'conditionnement' => $cond->quantite,
                'nb_conditionnements' => 0,
                'prix_unitaire' => $cond->prix,
                'total' => $reste * ($cond->prix / $cond->quantite)
            ];
        }
        return [ 'detail' => $detail, 'total' => $total ];
    }
}
