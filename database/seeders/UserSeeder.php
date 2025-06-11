<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Création des utilisateurs de base pour le système.
     */
    public function run(): void
    {
        // Création du rôle admin s'il n'existe pas déjà
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
        ], [
            'description' => 'Administrateur du système avec tous les droits'
        ]);

        // Création des autres rôles si nécessaire
        $gestionnaireRole = Role::firstOrCreate([
            'name' => 'gestionnaire',
        ], [
            'description' => 'Gestionnaire de superette avec droits étendus'
        ]);

        $caissierRole = Role::firstOrCreate([
            'name' => 'caissier',
        ], [
            'description' => 'Caissier avec accès limité aux opérations de caisse'
        ]);

        // Création de l'utilisateur administrateur
        $admin = User::firstOrCreate(
            ['email' => 'admin@gnonel.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('admin123'),
                'telephone' => '00123456789',
                'adresse' => 'Siège - Gnonel Superette',
                'actif' => true
            ]
        );

        // Attribution du rôle admin
        if (!$admin->hasRole('admin')) {
            $admin->roles()->attach($adminRole->id);
        }

        // Création d'un utilisateur gestionnaire
        $gestionnaire = User::firstOrCreate(
            ['email' => 'gestionnaire@gnonel.com'],
            [
                'name' => 'Gestionnaire',
                'password' => Hash::make('gestion123'),
                'telephone' => '00123456790',
                'adresse' => 'Boutique Principale',
                'actif' => true
            ]
        );

        // Attribution du rôle gestionnaire
        if (!$gestionnaire->hasRole('gestionnaire')) {
            $gestionnaire->roles()->attach($gestionnaireRole->id);
        }

        // Création d'un utilisateur caissier
        $caissier = User::firstOrCreate(
            ['email' => 'caissier@gnonel.com'],
            [
                'name' => 'Caissier',
                'password' => Hash::make('caisse123'),
                'telephone' => '00123456791',
                'adresse' => 'Boutique Principale',
                'actif' => true
            ]
        );

        // Attribution du rôle caissier
        if (!$caissier->hasRole('caissier')) {
            $caissier->roles()->attach($caissierRole->id);
        }
    }
}
