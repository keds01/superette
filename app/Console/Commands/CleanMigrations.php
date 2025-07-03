<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CleanMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:clean {--force : Force l\'exécution sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie les migrations en double ou conflictuelles dans la table des migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!Schema::hasTable('migrations')) {
            $this->error('La table migrations n\'existe pas.');
            return 1;
        }

        // Récupérer toutes les migrations
        $migrations = DB::table('migrations')->orderBy('id')->get();
        
        // Grouper par batch pour voir les migrations qui ont potentiellement des doublons
        $migrationsByBatch = $migrations->groupBy('batch');
        
        // Stocker les migrations par nom pour détecter les doublons
        $uniqueMigrations = [];
        $duplicateMigrations = [];
        $conflictingMigrations = [];
        
        // Identifier les migrations conflictuelles (mêmes tables ou colonnes)
        $tablePatterns = [
            '/create_(\w+)_table/' => 'create', 
            '/add_(\w+)_to_(\w+)_table/' => 'alter'
        ];
        $tableOperations = [];
        
        foreach ($migrations as $migration) {
            $migrationName = $migration->migration;
            
            // Détecter les conflits potentiels entre migrations
            foreach ($tablePatterns as $pattern => $operationType) {
                if (preg_match($pattern, $migrationName, $matches)) {
                    if ($operationType === 'create') {
                        $tableName = $matches[1];
                        if (!isset($tableOperations[$tableName])) {
                            $tableOperations[$tableName] = [];
                        }
                        $tableOperations[$tableName][] = [
                            'operation' => 'create',
                            'migration' => $migration
                        ];
                        
                        // S'il y a plusieurs créations pour la même table
                        if (count(array_filter($tableOperations[$tableName], fn($op) => $op['operation'] === 'create')) > 1) {
                            $conflictingMigrations[$tableName] = array_filter(
                                $tableOperations[$tableName], 
                                fn($op) => $op['operation'] === 'create'
                            );
                        }
                    } elseif ($operationType === 'alter') {
                        $columnName = $matches[1];
                        $tableName = $matches[2];
                        
                        if (!isset($tableOperations[$tableName])) {
                            $tableOperations[$tableName] = [];
                        }
                        
                        $tableOperations[$tableName][] = [
                            'operation' => 'alter',
                            'column' => $columnName,
                            'migration' => $migration
                        ];
                        
                        // Vérifier les modifications de colonne en double
                        $alterations = array_filter(
                            $tableOperations[$tableName], 
                            fn($op) => $op['operation'] === 'alter' && isset($op['column']) && $op['column'] === $columnName
                        );
                        
                        if (count($alterations) > 1) {
                            if (!isset($conflictingMigrations[$tableName])) {
                                $conflictingMigrations[$tableName] = [];
                            }
                            $conflictingMigrations[$tableName] = array_merge(
                                $conflictingMigrations[$tableName] ?? [],
                                $alterations
                            );
                        }
                    }
                }
            }
            
            // Détecter les migrations en double
            if (!isset($uniqueMigrations[$migrationName])) {
                $uniqueMigrations[$migrationName] = $migration;
            } else {
                $duplicateMigrations[$migrationName][] = $migration;
            }
        }
        
        // Afficher les migrations en double
        if (!empty($duplicateMigrations)) {
            $this->info('Migrations en double détectées:');
            foreach ($duplicateMigrations as $name => $duplicates) {
                $this->warn("- {$name} (IDs: " . implode(', ', array_map(fn($m) => $m->id, $duplicates)) . ")");
            }
        } else {
            $this->info('Aucune migration en double trouvée.');
        }
        
        // Afficher les migrations conflictuelles
        if (!empty($conflictingMigrations)) {
            $this->info('Migrations conflictuelles détectées:');
            foreach ($conflictingMigrations as $table => $conflicts) {
                $this->warn("- Table '{$table}': " . count($conflicts) . " opérations conflictuelles");
                foreach ($conflicts as $conflict) {
                    $this->line("  * " . $conflict['migration']->migration . " (ID: " . $conflict['migration']->id . ")");
                }
            }
        } else {
            $this->info('Aucune migration conflictuelle trouvée.');
        }
        
        // Proposer la correction
        if (!empty($duplicateMigrations) || !empty($conflictingMigrations)) {
            if (!$this->option('force') && !$this->confirm('Souhaitez-vous nettoyer ces migrations?', false)) {
                return 0;
            }
            
            $this->info('Nettoyage des migrations...');
            
            // Supprimer les migrations en double
            foreach ($duplicateMigrations as $name => $duplicates) {
                foreach ($duplicates as $duplicate) {
                    $this->line("Suppression de la migration en double: {$name} (ID: {$duplicate->id})");
                    DB::table('migrations')->where('id', $duplicate->id)->delete();
                }
            }
            
            // Garder uniquement la dernière migration conflictuelle pour chaque table
            foreach ($conflictingMigrations as $table => $conflicts) {
                // Trier par batch et id pour garder la plus récente
                usort($conflicts, function($a, $b) {
                    $batchDiff = $b['migration']->batch - $a['migration']->batch;
                    return $batchDiff !== 0 ? $batchDiff : $b['migration']->id - $a['migration']->id;
                });
                
                // Supprimer toutes sauf la plus récente
                array_shift($conflicts); // Garder la première (plus récente)
                
                foreach ($conflicts as $conflict) {
                    $this->line("Suppression de la migration conflictuelle: {$conflict['migration']->migration} (ID: {$conflict['migration']->id})");
                    DB::table('migrations')->where('id', $conflict['migration']->id)->delete();
                }
            }
            
            $this->info('Migrations nettoyées avec succès!');
        }
        
        return 0;
    }
} 