<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conditionnement extends Model
{
    protected $fillable = [
        'produit_id', 'type', 'quantite', 'prix'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
