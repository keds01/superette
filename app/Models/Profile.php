<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo',
        'telephone',
        'adresse',
        'ville',
        'pays',
        'bio',
        'preferences'
    ];

    protected $casts = [
        'preferences' => 'array'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    public function scopeParRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Méthodes
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/default-avatar.png');
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    public function isCaissier()
    {
        return $this->hasRole('caissier');
    }

    public function isGerant()
    {
        return $this->hasRole('gerant');
    }

    public function updatePreferences($key, $value)
    {
        $preferences = $this->preferences ?? [];
        $preferences[$key] = $value;
        $this->preferences = $preferences;
        $this->save();
    }

    public function getPreference($key, $default = null)
    {
        return $this->preferences[$key] ?? $default;
    }
}
