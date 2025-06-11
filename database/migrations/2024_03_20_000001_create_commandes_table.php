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
        Schema::create('commandes', function (Blueprint $table) {
    $table->id();
    $table->string('numero_commande')->unique();
    $table->unsignedBigInteger('fournisseur_id');
    $table->date('date_commande');
    $table->date('date_livraison_prevue')->nullable();
    $table->enum('statut', [
        'en_attente', 'validee', 'en_cours_livraison', 'partiellement_recue', 'recue', 'terminee', 'annulee'
    ])->default('en_attente');
    $table->decimal('montant_total', 15, 2)->default(0);
    $table->string('devise', 10)->default('EUR');
    $table->timestamps();

    $table->foreign('fournisseur_id')
        ->references('id')->on('fournisseurs')
        ->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
