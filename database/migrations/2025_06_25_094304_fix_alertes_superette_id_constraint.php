<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. S'assurer que tous les enregistrements ont une superette_id
        DB::statement('UPDATE alertes SET superette_id = 1 WHERE superette_id IS NULL');
        
        // 2. Supprimer la contrainte de clé étrangère existante
        Schema::table('alertes', function (Blueprint $table) {
            // Récupérer le nom de la contrainte
            $foreignKeys = DB::select(
                "SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'alertes' 
                AND COLUMN_NAME = 'superette_id' 
                AND CONSTRAINT_NAME LIKE 'alertes_%_foreign'"
            );
            
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $key) {
                    $table->dropForeign($key->CONSTRAINT_NAME);
                }
            } else {
                // Si la contrainte n'est pas trouvée, essayer avec le nom standard
                try {
                    $table->dropForeign(['superette_id']);
                } catch (\Exception $e) {
                    // Ignorer l'erreur si la contrainte n'existe pas
                }
            }
        });
        
        // 3. Modifier la colonne pour la rendre non nullable
        DB::statement('ALTER TABLE alertes MODIFY superette_id BIGINT UNSIGNED NOT NULL');
        
        // 4. Recréer la contrainte avec CASCADE au lieu de SET NULL
        Schema::table('alertes', function (Blueprint $table) {
            $table->foreign('superette_id')
                  ->references('id')
                  ->on('superettes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Supprimer la contrainte CASCADE
        Schema::table('alertes', function (Blueprint $table) {
            $table->dropForeign(['superette_id']);
        });
        
        // 2. Rendre la colonne nullable
        DB::statement('ALTER TABLE alertes MODIFY superette_id BIGINT UNSIGNED NULL');
        
        // 3. Recréer la contrainte avec SET NULL
        Schema::table('alertes', function (Blueprint $table) {
            $table->foreign('superette_id')
                  ->references('id')
                  ->on('superettes')
                  ->onDelete('set null');
        });
    }
}; 