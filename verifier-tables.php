<?php

require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Tableaux pour stocker les informations
$tables = ['roles', 'permissions', 'role_permission', 'user_role'];
$tableInfo = [];

echo "=== VÉRIFICATION DES TABLES DE SÉCURITÉ ===\n\n";

// Vérifier si les tables existent
foreach ($tables as $table) {
    $exists = Schema::hasTable($table);
    echo "Table '$table': " . ($exists ? "Existe" : "N'existe pas") . "\n";
    
    if ($exists) {
        // Récupérer la structure de la table
        $columns = DB::select("SHOW COLUMNS FROM {$table}");
        echo "Colonnes:\n";
        foreach ($columns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
        echo "\n";
    }
}

// Exécute une migration spécifique si besoin
echo "=== TENTATIVE D'EXÉCUTION DE LA MIGRATION MANQUANTE ===\n\n";
try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    // Supprimer les tables si elles existent mais sont mal structurées
    if (Schema::hasTable('role_permission')) DB::statement('DROP TABLE role_permission');
    if (Schema::hasTable('user_role')) DB::statement('DROP TABLE user_role');
    if (Schema::hasTable('permissions')) DB::statement('DROP TABLE permissions');
    if (Schema::hasTable('roles')) DB::statement('DROP TABLE roles');
    
    // Créer les tables avec la bonne structure
    if (!Schema::hasTable('roles')) {
        Schema::create('roles', function ($table) {
            $table->id();
            $table->string('name')->unique(); // Utilise 'name' au lieu de 'nom'
            $table->string('description')->nullable();
            $table->timestamps();
        });
        echo "Table 'roles' créée avec succès.\n";
    }

    if (!Schema::hasTable('permissions')) {
        Schema::create('permissions', function ($table) {
            $table->id();
            $table->string('name')->unique(); // Utilise 'name' au lieu de 'nom'
            $table->string('description')->nullable();
            $table->timestamps();
        });
        echo "Table 'permissions' créée avec succès.\n";
    }

    if (!Schema::hasTable('role_permission')) {
        Schema::create('role_permission', function ($table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });
        echo "Table 'role_permission' créée avec succès.\n";
    }

    if (!Schema::hasTable('user_role')) {
        Schema::create('user_role', function ($table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });
        echo "Table 'user_role' créée avec succès.\n";
    }
    
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "Migration terminée avec succès.\n";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
