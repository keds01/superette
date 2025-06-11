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
            ProduitsSystemSeeder::class,
            UserSeeder::class,          // Seeder pour les utilisateurs et rôles
            PromotionSeeder::class, // Ajouté pour la promo riz
            RolesAndPermissionsSeeder::class, // Notre nouveau seeder pour rôles et permissions
            // PermissionSeeder::class,    // Ancien seeder pour les permissions (remplacé)
            EmployeSeeder::class,       // Seeder pour les employés
            UnitSeeder::class,
            // ProductSeeder::class,          // Fichier manquant, commenté temporairement
            // ClientSeeder::class,          // Fichier manquant, commenté temporairement
            // EmployeSeeder::class,         // Fichier manquant, commenté temporairement
            // StockMovementSeeder::class,    // Fichier manquant, commenté temporairement
            // AlertSeeder::class,            // Fichier manquant, commenté temporairement
            // VenteSeeder::class,            // Fichier manquant, commenté temporairement
            // CaisseSeeder::class,           // Fichier manquant, commenté temporairement
            SuperAdminSeeder::class,
            DefaultEmployeSeeder::class,
            ReceptionSeeder::class,      // Seeder pour les réceptions et leurs détails
            CommandeSeeder::class,       // Seeder pour les commandes fournisseurs et leurs détails
            // Ajoute ici d'autres seeders si nécessaire
        ]);
    }
}
