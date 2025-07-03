<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            // Supprimer la contrainte d'unicité existante sur code_barres
            $table->dropUnique('produits_code_barres_unique');
            
            // Ajouter une nouvelle contrainte d'unicité composite sur code_barres et superette_id
            $table->unique(['code_barres', 'superette_id'], 'produits_code_barres_superette_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            // Supprimer la contrainte composite
            $table->dropUnique('produits_code_barres_superette_unique');
            
            // Restaurer la contrainte d'unicité simple sur code_barres
            $table->unique('code_barres', 'produits_code_barres_unique');
        });
    }
};
