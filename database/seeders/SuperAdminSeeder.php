<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Crée ou met à jour le super admin avec toutes les permissions.
     */
    public function run(): void
    {
        // Création ou récupération du rôle super-admin
        $role = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['description' => 'Super administrateur, tous droits']
        );

        // Création ou mise à jour de l'utilisateur eloyisfrx uniquement
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'eloyisfrx@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('Impo$$ible2Pir@ter'),
                'telephone' => '90859019',
                'adresse' => 'lome',
                'actif' => true
            ]
        );

        // Associer le rôle super-admin à eloyisfrx
        $user->assignRole($role);
    }
}
