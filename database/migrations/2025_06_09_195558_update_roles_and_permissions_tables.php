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
        // Désactiver les contraintes de clé étrangère temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Suppression des tables si elles existent
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        
        // Création de la table 'roles' avec la structure correcte
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // Renommé de 'nom' à 'name'
            $table->string('guard_name')->default('web');  // Ajouté pour la compatibilité
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        // Création de la table 'permissions' avec la structure correcte
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // Renommé de 'nom' à 'name'
            $table->string('guard_name')->default('web');  // Ajouté pour la compatibilité
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        // Création de la table pivot 'role_permission'
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });
        
        // Création de la table pivot 'user_role'
        Schema::create('user_role', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On ne fait rien en cas de rollback pour éviter la perte de données
        // Si vraiment nécessaire, on pourrait recréer les tables avec l'ancienne structure
    }
};
