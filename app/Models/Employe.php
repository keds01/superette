<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employe extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'poste',
        'statut',
        'adresse',
        'date_embauche',
        'salaire',
        'notes'
    ];

    protected $dates = [
        'date_embauche',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
