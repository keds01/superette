<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FactureFournisseur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero',
        'fournisseur_id',
        'approvisionnement_id',
        'date_emission',
        'date_echeance',
        'montant_ht',
        'tva',
        'montant_tva',
        'montant_ttc',
        'montant_paye',
        'montant_restant',
        'statut',
        'notes',
        'fichier_facture'
    ];

    protected $casts = [
        'date_emission' => 'datetime',
        'date_echeance' => 'datetime',
        'montant_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_restant' => 'decimal:2'
    ];

    // Relations
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function approvisionnement()
    {
        return $this->belongsTo(Approvisionnement::class);
    }

    public function paiements()
    {
        return $this->hasMany(PaiementApprovisionnement::class);
    }

    // Méthodes
    public function calculerMontants()
    {
        $this->montant_tva = $this->montant_ht * ($this->tva / 100);
        $this->montant_ttc = $this->montant_ht + $this->montant_tva;
        $this->montant_paye = $this->paiements->sum('montant');
        $this->montant_restant = $this->montant_ttc - $this->montant_paye;
        $this->save();
    }

    public function estCompletementPayee()
    {
        return $this->montant_restant <= 0;
    }

    public function getPourcentagePayeAttribute()
    {
        if ($this->montant_ttc == 0) {
            return 0;
        }
        return round(($this->montant_paye / $this->montant_ttc) * 100, 2);
    }

    public function getJoursRetardAttribute()
    {
        if ($this->statut === 'payee' || !$this->date_echeance) {
            return 0;
        }
        return max(0, now()->diffInDays($this->date_echeance, false));
    }

    public function marquerCommePayee()
    {
        if ($this->estCompletementPayee()) {
            $this->statut = 'payee';
            $this->save();
        }
    }

    public function annuler()
    {
        if ($this->statut === 'annulee') {
            return;
        }

        // Vérifier si des paiements ont été effectués
        if ($this->montant_paye > 0) {
            throw new \Exception('Impossible d\'annuler une facture avec des paiements effectués.');
        }

        $this->statut = 'annulee';
        $this->save();
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopePayees($query)
    {
        return $query->where('statut', 'payee');
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
        return $query->where('statut', 'en_attente')
            ->where('date_echeance', '<', now());
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