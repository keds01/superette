<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Vérification si la table roles existe déjà
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('nom')->unique();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // Vérification si la table permissions existe déjà
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('nom')->unique();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // Vérification si la table role_permission existe déjà
        if (!Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->foreignId('permission_id')->constrained()->onDelete('cascade');
                $table->primary(['role_id', 'permission_id']);
            });
        }

        // Vérification si la table user_role existe déjà
        if (!Schema::hasTable('user_role')) {
            Schema::create('user_role', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->primary(['user_id', 'role_id']);
            });
        }
    }

    public function down()
    {
        // Ne rien faire en cas de rollback pour éviter de supprimer des données importantes
    }
};
