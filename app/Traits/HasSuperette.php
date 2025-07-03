<?php

namespace App\Traits;

use App\Models\Superette;
use App\Scopes\SuperetteScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasSuperette
{
    /**
     * Boot the trait with le global scope.
     */
    public static function bootHasSuperette()
    {
        static::addGlobalScope(new SuperetteScope);
    }

    /**
     * Relation vers la superette à laquelle appartient cet enregistrement
     */
    public function superette(): BelongsTo
    {
        return $this->belongsTo(Superette::class);
    }
    
    /**
     * Assigne une superette à l'enregistrement courant
     *
     * @param int|Superette $superette
     * @return $this
     */
    public function assignSuperette($superette)
    {
        $superetteId = $superette instanceof Superette ? $superette->id : $superette;
        $this->superette_id = $superetteId;
        return $this;
    }
} 