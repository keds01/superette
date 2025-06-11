<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Produit extends Model
{
    /**
     * Valeur par défaut du délai d'alerte avant péremption (en jours)
     */
    const DELAI_ALERTE_PEREMPTION_DEFAUT = 30;
    
    use HasFactory, SoftDeletes;

    protected $table = 'produits';

    protected $fillable = [
        'nom',
        'reference',
        'code_barres',
        'description',
        'categorie_id',
        'unite_vente_id',
        'conditionnement_fournisseur',
        'quantite_par_conditionnement',
        'stock',
        'seuil_alerte',
        'emplacement_rayon',
        'emplacement_etagere',
        'date_peremption',
        'delai_alerte_peremption',
        'prix_achat_ht',
        'prix_vente_ht',
        'prix_vente_ttc',
        'marge',
        'tva',
        'image',
        'actif'
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
    // public function getStockActuelAttribute()
    // {
    //     // Le champ gerer_stock n'existe pas dans la migration.
    //     // La colonne 'stock' est la source de vérité.
    //     // if (!$this->gerer_stock) {
    //     //     return null;
    //     // }

    //     // return $this->mouvementsStock() // Supposant que stocks() était une typo pour mouvementsStock()
    //     //     ->where('type_mouvement', 'entree')
    //     //     ->sum('quantite') - 
    //     //     $this->mouvementsStock()
    //     //     ->where('type_mouvement', 'sortie')
    //     //     ->sum('quantite');
    // }

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
}
