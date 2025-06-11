<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Crée ou met à jour le super admin avec toutes les permissions.
     */
    public function run(): void
    {
        // 1. Création ou récupération du rôle super_admin
        $role = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['description' => 'Super administrateur, tous droits']
        );

        // 2. Création ou récupération de l'utilisateur super admin
        $user = User::firstOrCreate(
            ['email' => 'superadmin@gnonel.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('superadmin123'),
                'telephone' => '0000000001',
                'adresse' => 'Siège - Gnonel Superette',
                'actif' => true
            ]
        );

        // 3. Association du rôle à l'utilisateur
        $user->roles()->syncWithoutDetaching([$role->id]);

        // 4. Récupération de toutes les permissions
        $permissions = Permission::pluck('id')->toArray();

        // 5. Association de toutes les permissions au rôle super_admin
        $role->permissions()->syncWithoutDetaching($permissions);

        // 6. (Optionnel) Association directe à l'utilisateur
        $user->permissions()->syncWithoutDetaching($permissions);
    }
}
