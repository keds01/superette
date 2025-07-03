<?php
/**
 * Script de correction des problèmes courants de base de données pour le projet gestion_superette
 * 
 * Ce script peut être exécuté directement depuis la racine du projet:
 * php fix-db-issues.php
 */

// Bootstrap l'application Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use App\Models\MouvementStock;
use App\Models\Superette;
use App\Models\User;

echo "========================================\n";
echo "🛠️  Outil de correction des problèmes de BDD\n";
echo "========================================\n\n";

// 1. Vérifier et corriger le softDelete dans MouvementStock
echo "1️⃣  Vérification de la colonne deleted_at dans mouvements_stock...\n";
if (!Schema::hasTable('mouvements_stock')) {
    echo "   ⚠️  La table mouvements_stock n'existe pas encore.\n";
} else {
    if (!Schema::hasColumn('mouvements_stock', 'deleted_at')) {
        echo "   ⚙️  Ajout de la colonne deleted_at à mouvements_stock...\n";
        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->softDeletes();
        });
        echo "   ✅ Colonne deleted_at ajoutée avec succès!\n";
    } else {
        echo "   ✅ La colonne deleted_at existe déjà dans mouvements_stock.\n";
    }
}

// 2. Nettoyage des migrations en double
echo "\n2️⃣  Nettoyage des migrations en double...\n";
if (!Schema::hasTable('migrations')) {
    echo "   ⚠️  La table migrations n'existe pas encore.\n";
} else {
    // Détecter les migrations en double
    $migrations = DB::table('migrations')->orderBy('id')->get();
    $uniqueMigrations = [];
    $duplicateMigrations = [];
    $conflictMigrations = [];
    
    foreach ($migrations as $migration) {
        $name = $migration->migration;
        if (!isset($uniqueMigrations[$name])) {
            $uniqueMigrations[$name] = $migration;
        } else {
            if (!isset($duplicateMigrations[$name])) {
                $duplicateMigrations[$name] = [];
            }
            $duplicateMigrations[$name][] = $migration;
        }
        
        // Détecter les migrations conflictuelles (création de table en double)
        if (preg_match('/create_(\w+)_table/', $name, $matches)) {
            $tableName = $matches[1];
            if (!isset($conflictMigrations[$tableName])) {
                $conflictMigrations[$tableName] = [];
            }
            $conflictMigrations[$tableName][] = $migration;
        }
    }
    
    // Afficher et corriger les migrations en double
    if (empty($duplicateMigrations)) {
        echo "   ✅ Aucune migration en double trouvée.\n";
    } else {
        foreach ($duplicateMigrations as $name => $migrations) {
            echo "   🔄 Suppression de " . count($migrations) . " migration(s) en double pour: {$name}\n";
            foreach ($migrations as $migration) {
                DB::table('migrations')->where('id', $migration->id)->delete();
            }
        }
        echo "   ✅ Migrations en double supprimées!\n";
    }
    
    // Afficher et corriger les migrations conflictuelles
    $cleanedTables = [];
    foreach ($conflictMigrations as $table => $migrations) {
        if (count($migrations) > 1) {
            echo "   🔄 Plusieurs migrations créent la table: {$table}\n";
            // Garder seulement la migration la plus récente
            usort($migrations, function ($a, $b) {
                return $b->batch - $a->batch;
            });
            
            // Supprimer toutes sauf la première
            array_shift($migrations);
            foreach ($migrations as $migration) {
                echo "      - Suppression de la migration: {$migration->migration}\n";
                DB::table('migrations')->where('id', $migration->id)->delete();
            }
            $cleanedTables[] = $table;
        }
    }
    
    if (!empty($cleanedTables)) {
        echo "   ✅ Migrations conflictuelles résolues pour: " . implode(', ', $cleanedTables) . "\n";
    } else {
        echo "   ✅ Aucune migration conflictuelle trouvée.\n";
    }
}

// 3. Correction des références manquantes aux superettes
echo "\n3️⃣  Vérification des références aux superettes...\n";
$defaultSuperette = null;

// Vérifier si la table superettes existe
if (!Schema::hasTable('superettes')) {
    echo "   ⚠️  La table superettes n'existe pas encore!\n";
} else {
    // Vérifier s'il y a au moins une superette
    $superetteCount = Superette::count();
    if ($superetteCount == 0) {
        echo "   ⚙️  Création d'une superette par défaut...\n";
        try {
            $defaultSuperette = Superette::create([
                'nom' => 'Superette Principale',
                'adresse' => 'Adresse par défaut',
                'code' => 'DEFAULT',
                'actif' => true
            ]);
            echo "   ✅ Superette par défaut créée avec ID: " . $defaultSuperette->id . "\n";
        } catch (\Exception $e) {
            echo "   ❌ Erreur lors de la création de la superette par défaut: " . $e->getMessage() . "\n";
        }
    } else {
        $defaultSuperette = Superette::first();
        echo "   ✅ Superette par défaut trouvée: " . $defaultSuperette->nom . " (ID: " . $defaultSuperette->id . ")\n";
    }
    
    // Vérifier les utilisateurs sans superette
    if ($defaultSuperette && Schema::hasTable('users') && Schema::hasColumn('users', 'superette_id')) {
        $usersWithoutSuperette = User::whereNull('superette_id')->count();
        if ($usersWithoutSuperette > 0) {
            echo "   ⚙️  Association de {$usersWithoutSuperette} utilisateur(s) sans superette...\n";
            User::whereNull('superette_id')->update(['superette_id' => $defaultSuperette->id]);
            echo "   ✅ Utilisateurs associés à la superette par défaut.\n";
        } else {
            echo "   ✅ Tous les utilisateurs sont déjà associés à une superette.\n";
        }
    }
}

echo "\n========================================\n";
echo "🎉 Corrections terminées!\n";
echo "========================================\n\n";

echo "Conseils pour terminer la résolution:\n\n";
echo "1. Exécutez les migrations restantes: php artisan migrate\n";
echo "2. Si des problèmes persistent, utilisez: php artisan migrations:clean\n";
echo "3. Pour un diagnostic complet de la base de données: php artisan db:show\n\n";

echo "Bonne continuation avec votre application de gestion de superette! 😊\n"; 