<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approvisionnement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero',
        'fournisseur_id',
        'user_id',
        'date_commande',
        'date_livraison_prevue',
        'date_livraison_reelle',
        'montant_total',
        'montant_paye',
        'montant_restant',
        'statut',
        'mode_paiement',
        'reference_paiement',
        'notes'
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'date_livraison_prevue' => 'datetime',
        'date_livraison_reelle' => 'datetime',
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_restant' => 'decimal:2'
    ];

    // Relations
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(DetailApprovisionnement::class);
    }

    public function factures()
    {
        return $this->hasMany(FactureFournisseur::class);
    }

    public function paiements()
    {
        return $this->hasMany(PaiementApprovisionnement::class);
    }

    // Méthodes
    public function calculerMontants()
    {
        $this->montant_total = $this->details->sum(function ($detail) {
            return $detail->quantite * $detail->prix_unitaire;
        });

        $this->montant_paye = $this->paiements->sum('montant');
        $this->montant_restant = $this->montant_total - $this->montant_paye;
        $this->save();
    }

    public function estCompletementPaye()
    {
        return $this->montant_restant <= 0;
    }

    public function getPourcentagePayeAttribute()
    {
        if ($this->montant_total == 0) {
            return 0;
        }
        return round(($this->montant_paye / $this->montant_total) * 100, 2);
    }

    public function getDelaiLivraisonAttribute()
    {
        if (!$this->date_livraison_reelle) {
            return null;
        }
        return $this->date_livraison_reelle->diffInDays($this->date_commande);
    }

    public function getRetardLivraisonAttribute()
    {
        if (!$this->date_livraison_reelle || !$this->date_livraison_prevue) {
            return null;
        }
        return $this->date_livraison_reelle->diffInDays($this->date_livraison_prevue);
    }

    public function marquerCommeLivree()
    {
        $this->date_livraison_reelle = now();
        $this->statut = 'livree';
        $this->save();

        // Mettre à jour le stock pour chaque produit
        foreach ($this->details as $detail) {
            $produit = $detail->produit;
            $produit->quantite_stock += $detail->quantite;
            $produit->save();

            // Enregistrer le mouvement de stock
            MouvementStock::create([
                'produit_id' => $produit->id,
                'type' => 'entree',
                'quantite' => $detail->quantite,
                'prix_unitaire' => $detail->prix_unitaire,
                'motif' => 'Réception approvisionnement #' . $this->numero,
                'user_id' => auth()->id()
            ]);
        }
    }

    public function annuler()
    {
        if ($this->statut === 'annulee') {
            return;
        }

        // Vérifier si des paiements ont été effectués
        if ($this->montant_paye > 0) {
            throw new \Exception('Impossible d\'annuler un approvisionnement avec des paiements effectués.');
        }

        $this->statut = 'annulee';
        $this->save();
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeLivrees($query)
    {
        return $query->where('statut', 'livree');
    }

    public function scopeAnnulees($query)
    {
        return $query->where('statut', 'annulee');
    }

    public function scopeNonPayees($query)
    {
        return $query->whereRaw('montant_restant > 0');
    }

    public function scopeEnRetard($query)
    {
        return $query->where('statut', 'en_cours')
            ->where('date_livraison_prevue', '<', now());
    }

    public function scopeRecherche($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('numero', 'like', "%{$term}%")
              ->orWhereHas('fournisseur', function ($q) use ($term) {
                  $q->where('nom', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%");
              });
        });
    }
} 