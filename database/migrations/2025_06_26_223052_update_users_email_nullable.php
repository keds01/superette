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
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la contrainte unique sur email si elle existe
            $table->dropUnique(['email']);
            
            // Modifier la colonne email pour la rendre nullable
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurer la colonne email comme non-nullable
            $table->string('email')->nullable(false)->change();
            
            // Restaurer la contrainte unique sur email
            $table->unique('email');
        });
    }
};
