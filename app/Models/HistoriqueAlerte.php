<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueAlerte extends Model
{
    use HasFactory;

    protected $fillable = [
        'alerte_id',
        'message',
        'resolue'
    ];

    protected $casts = [
        'resolue' => 'boolean'
    ];

    public function alerte()
    {
        return $this->belongsTo(Alerte::class);
    }

    public function marquerCommeResolue()
    {
        $this->update(['resolue' => true]);
    }
} 