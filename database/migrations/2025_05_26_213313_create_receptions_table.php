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
        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->dateTime('date_reception');
            $table->string('numero_facture')->nullable();
            $table->enum('mode_paiement', ['especes', 'cheque', 'virement', 'autre']);
            $table->text('description')->nullable();
            $table->enum('statut', ['en_cours', 'terminee', 'annulee'])->default('en_cours');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receptions');
    }
};
