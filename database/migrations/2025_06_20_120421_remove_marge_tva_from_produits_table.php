<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cette migration est censée supprimer les colonnes marge et tva
     * mais ces colonnes sont ensuite réajoutées par une migration ultérieure
     */
    public function up(): void
    {
        // Ne rien faire car les colonnes sont nécessaires au fonctionnement de l'application
        /*
        Schema::table('produits', function (Blueprint $table) {
            // Actions supprimées car les colonnes sont nécessaires
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rien à annuler
        /*
        Schema::table('produits', function (Blueprint $table) {
            // 
        });
        */
    }
};