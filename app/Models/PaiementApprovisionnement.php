<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaiementApprovisionnement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'approvisionnement_id',
        'facture_id',
        'date_paiement',
        'montant',
        'mode_paiement',
        'reference_paiement',
        'banque',
        'numero_cheque',
        'date_cheque',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'date_paiement' => 'datetime',
        'date_cheque' => 'datetime',
        'montant' => 'decimal:2'
    ];

    // Relations
    public function approvisionnement()
    {
        return $this->belongsTo(Approvisionnement::class);
    }

    public function facture()
    {
        return $this->belongsTo(FactureFournisseur::class, 'facture_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes
    public function enregistrerPaiement()
    {
        // Mettre à jour le montant payé de l'approvisionnement
        $this->approvisionnement->calculerMontants();

        // Mettre à jour le montant payé de la facture si elle existe
        if ($this->facture) {
            $this->facture->calculerMontants();
            $this->facture->marquerCommePayee();
        }

        // Mettre à jour le solde du fournisseur
        $fournisseur = $this->approvisionnement->fournisseur;
        $fournisseur->solde_actuel -= $this->montant;
        $fournisseur->save();
    }

    public function annuler()
    {
        // Restaurer le montant payé de l'approvisionnement
        $this->approvisionnement->calculerMontants();

        // Restaurer le montant payé de la facture si elle existe
        if ($this->facture) {
            $this->facture->calculerMontants();
        }

        // Restaurer le solde du fournisseur
        $fournisseur = $this->approvisionnement->fournisseur;
        $fournisseur->solde_actuel += $this->montant;
        $fournisseur->save();

        // Supprimer le paiement
        $this->delete();
    }

    // Scopes
    public function scopeParModePaiement($query, $mode)
    {
        return $query->where('mode_paiement', $mode);
    }

    public function scopeParPeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_paiement', [$debut, $fin]);
    }

    public function scopeParFournisseur($query, $fournisseurId)
    {
        return $query->whereHas('approvisionnement', function ($q) use ($fournisseurId) {
            $q->where('fournisseur_id', $fournisseurId);
        });
    }

    public function scopeParUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
} 