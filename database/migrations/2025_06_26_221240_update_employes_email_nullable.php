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
        // Sécurisation : suppression de l'index unique seulement s'il existe
        try {
            Schema::table('employes', function (Blueprint $table) {
                // Laravel attend le nom exact de l'index
                $table->dropUnique('employes_email_unique');
            });
        } catch (\Exception $e) {
            // L'index n'existe pas, on ignore l'erreur
        }
        // Modifier la colonne email pour la rendre nullable
        Schema::table('employes', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            // Restaurer la contrainte unique sur email
            $table->string('email')->nullable(false)->change();
            $table->unique('email');
        });
    }
};
