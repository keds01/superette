<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Constantes pour les rôles disponibles
     */
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_RESPONSABLE = 'responsable';
    const ROLE_CAISSIER = 'caissier';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'adresse',
        'actif',
        'role',
        'superette_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean'
        ];
    }

    public function employe()
    {
        return $this->hasOne(Employe::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    
    /**
     * Relation avec la superette assignée à cet utilisateur
     */
    public function superette()
    {
        return $this->belongsTo(\App\Models\Superette::class);
    }
    
    /**
     * Relation avec les logs d'activité de cet utilisateur
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
    
    // Gestion des rôles simplifiée
    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }
    
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_SUPER_ADMIN;
    }
    
    public function isResponsable(): bool
    {
        return $this->role === self::ROLE_RESPONSABLE;
    }
    
    public function isCaissier(): bool
    {
        return $this->role === self::ROLE_CAISSIER;
    }

    /**
     * Vérifie si l'utilisateur possède une permission donnée via son rôle.
     */
    public function hasPermissionTo(string $permission): bool
    {
        // Super admin a toutes les permissions
        if ($this->isSuperAdmin()) {
            return true;
        }
        // Si l'utilisateur a un rôle relié à des permissions
        if (method_exists($this, 'role') && $this->role) {
            // On suppose que le champ 'role' correspond au nom du rôle
            $roleModel = \App\Models\Role::where('nom', $this->role)->first();
            if ($roleModel && method_exists($roleModel, 'permissions')) {
                return $roleModel->permissions()->where('nom', $permission)->exists();
            }
        }
        return false;
    }
}
