<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Ajout de la référence au ReceptionSeeder si ce n'est pas déjà fait par l'IDE, mais Laravel le gère bien souvent.
// use Database\Seeders\ReceptionSeeder; // Pas strictement nécessaire si dans le même namespace et appelé via ::class

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UnitesSystemSeeder::class, // Notre seeder pour les unités système
            CategoriesSystemSeeder::class,
            // ProduitsSystemSeeder::class, // Supprimé
            // RolesAndPermissionsSeeder::class, // Supprimé
            // UserSeeder::class,          // Supprimé
            SuperetteSeeder::class,     // Ajout du seeder de superette par défaut
            // PromotionSeeder::class, // Supprimé
            // EmployeSeeder::class,       // Supprimé
            UnitSeeder::class,
            // VenteSeeder::class,         // Supprimé
            // SuperAdminSeeder::class, // Désactivé (Spatie)
            // PermissionSeeder::class, // Désactivé (Spatie)
            // RolesSeeder::class, // Désactivé (Spatie)
            // UserWithRolesSeeder::class, // Désactivé (utilisateurs de test)
            SuperAdminUserSeeder::class,
            // Ajoute ici d'autres seeders si nécessaire
        ]);
    }
}
