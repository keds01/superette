<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Superette extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'email',
        'code',
        'description',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations principales
    public function produits() { return $this->hasMany(Produit::class); }
    public function employes() { return $this->hasMany(Employe::class); }
    public function ventes() { return $this->hasMany(Vente::class); }
    public function clients() { return $this->hasMany(Client::class); }
    public function fournisseurs() { return $this->hasMany(Fournisseur::class); }
    public function stocks() { return $this->hasMany(Stock::class); }
    public function mouvementsStock() { return $this->hasMany(MouvementStock::class); }
    public function alertes() { return $this->hasMany(Alerte::class); }
    public function promotions() { return $this->hasMany(Promotion::class); }
    public function remises() { return $this->hasMany(Remise::class); }
    public function caisses() { return $this->hasMany(Caisse::class); }
    public function approvisionnements() { return $this->hasMany(Approvisionnement::class); }
    public function receptions() { return $this->hasMany(Reception::class); }
    public function paiements() { return $this->hasMany(Paiement::class); }

    /**
     * Utilisateurs rattachés à cette superette
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'superette_id');
    }

    // Scope pour les superettes actives
    public function scopeActive($query) { return $query->where('actif', true); }
    public function scopeInactive($query) { return $query->where('actif', false); }

    // Génération automatique du code unique
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($superette) {
            if (empty($superette->code)) {
                $superette->code = self::generateUniqueCode($superette->nom);
            }
        });
    }
    protected static function generateUniqueCode($nom)
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nom), 0, 3));
        $code = $prefix . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
        while (self::where('code', $code)->exists()) {
            $code = $prefix . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT);
        }
        return $code;
    }
}
