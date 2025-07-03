<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DebugConditionnements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:conditionnements {produit_id?} {--create : Créer un conditionnement de test} {--check-structure : Vérifier la structure de la table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Affiche les conditionnements d\'un produit ou de tous les produits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $produitId = $this->argument('produit_id');
        $shouldCreate = $this->option('create');
        $checkStructure = $this->option('check-structure');
        
        // Vérifier si la table existe
        if (!Schema::hasTable('conditionnements')) {
            $this->error('La table conditionnements n\'existe pas!');
            return 1;
        }
        
        // Vérifier la structure de la table
        if ($checkStructure) {
            $this->info('Vérification de la structure de la table conditionnements:');
            $columns = Schema::getColumnListing('conditionnements');
            $this->table(['Colonne'], array_map(function($column) {
                return [$column];
            }, $columns));
            
            // Vérifier les contraintes de clé étrangère d'une manière plus compatible
            $this->info('Vérification des contraintes de clé étrangère:');
            try {
                // Cette requête est plus compatible avec MySQL
                $foreignKeys = DB::select("
                    SELECT 
                        TABLE_NAME as 'table_name',
                        COLUMN_NAME as 'column_name',
                        REFERENCED_TABLE_NAME as 'foreign_table_name',
                        REFERENCED_COLUMN_NAME as 'foreign_column_name'
                    FROM
                        INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE
                        TABLE_NAME = 'conditionnements'
                        AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                if (count($foreignKeys) > 0) {
                    $this->table(['Table', 'Colonne', 'Table référencée', 'Colonne référencée'], 
                        array_map(function($fk) {
                            return [
                                $fk->table_name,
                                $fk->column_name,
                                $fk->foreign_table_name,
                                $fk->foreign_column_name
                            ];
                        }, $foreignKeys));
                } else {
                    $this->warn('Aucune contrainte de clé étrangère trouvée pour la table conditionnements');
                }
            } catch (\Exception $e) {
                $this->error("Erreur lors de la vérification des contraintes: " . $e->getMessage());
            }
        }
        
        if ($produitId) {
            $this->info("Conditionnements pour le produit ID: $produitId");
            $conditionnements = DB::table('conditionnements')->where('produit_id', $produitId)->get();
            
            if ($conditionnements->count() > 0) {
                $this->table(['ID', 'Produit ID', 'Type', 'Quantité', 'Prix', 'Créé le'], 
                    $conditionnements->map(function($cond) {
                        return [
                            $cond->id,
                            $cond->produit_id,
                            $cond->type,
                            $cond->quantite,
                            $cond->prix,
                            $cond->created_at
                        ];
                    })->toArray());
            } else {
                $this->warn("Aucun conditionnement trouvé pour le produit ID: $produitId");
            }
            
            // Vérifier que le produit existe
            $produit = \DB::table('produits')->find($produitId);
            if ($produit) {
                $this->info("Produit trouvé: " . $produit->nom);
            } else {
                $this->error("Produit non trouvé avec l'ID: $produitId");
            }
        } else {
            $this->info("Liste de tous les conditionnements:");
            $conditionnements = DB::table('conditionnements')->get();
            
            if ($conditionnements->count() > 0) {
                $this->table(['ID', 'Produit ID', 'Type', 'Quantité', 'Prix', 'Créé le'], 
                    $conditionnements->map(function($cond) {
                        return [
                            $cond->id,
                            $cond->produit_id,
                            $cond->type,
                            $cond->quantite,
                            $cond->prix,
                            $cond->created_at
                        ];
                    })->toArray());
            } else {
                $this->warn("Aucun conditionnement trouvé");
            }
        }
        
        if ($shouldCreate && $produitId) {
            $this->info("Création d'un conditionnement de test pour le produit ID: $produitId");
            
            try {
                // Vérifier que le produit existe
                $produit = \App\Models\Produit::find($produitId);
                if (!$produit) {
                    $this->error("Produit non trouvé avec l'ID: $produitId");
                    return 1;
                }
                
                // Créer un conditionnement de test
                $conditionnement = $produit->conditionnements()->create([
                    'type' => 'Pack de test',
                    'quantite' => 6,
                    'prix' => $produit->prix_vente_ttc * 5.5, // Prix légèrement réduit pour le pack
                ]);
                
                $this->info("Conditionnement créé avec succès!");
                $this->table(['ID', 'Produit ID', 'Type', 'Quantité', 'Prix'], [
                    [
                        $conditionnement->id,
                        $conditionnement->produit_id,
                        $conditionnement->type,
                        $conditionnement->quantite,
                        $conditionnement->prix
                    ]
                ]);
                
            } catch (\Exception $e) {
                $this->error("Erreur lors de la création du conditionnement: " . $e->getMessage());
                $this->line("Trace: " . $e->getTraceAsString());
                return 1;
            }
        }
        
        return 0;
    }
}
