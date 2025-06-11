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
        // Renommer le fichier pour qu'il s'exécute après la création de la table produits
        // Le nouveau nom sera : 2024_03_21_000010_create_alertes_table.php
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->nullable()->constrained('produits')->onDelete('cascade');
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->enum('type', ['seuil_minimum', 'seuil_maximum', 'peremption', 'mouvement_important', 'valeur_stock', 'stock_bas']);
            $table->integer('seuil')->nullable();
            $table->integer('periode')->nullable();
            $table->string('message')->nullable();
            $table->boolean('estDeclenchee')->default(false);
            $table->string('notification_email')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['type', 'estDeclenchee']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
}; 