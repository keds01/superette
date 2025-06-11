<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            // Supprimer d'abord la contrainte de clé étrangère
            // $table->dropForeign(['categorie_id']);
            // Ensuite supprimer la colonne
            // $table->dropColumn('categorie_id');
            // Ajouter la colonne 'periode' si elle n'existe pas
            if (!Schema::hasColumn('alertes', 'periode')) {
                $table->integer('periode')->nullable()->after('seuil');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            // $table->foreignId('categorie_id')->nullable()->constrained('categories');
            // Supprimer la colonne 'periode' si elle existe
            if (Schema::hasColumn('alertes', 'periode')) {
                $table->dropColumn('periode');
            }
        });
    }
}; 