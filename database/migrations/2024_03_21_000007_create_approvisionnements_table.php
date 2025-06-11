<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approvisionnements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('numero_commande')->unique();
            $table->date('date_commande');
            $table->date('date_livraison_prevue')->nullable();
            $table->date('date_livraison_reelle')->nullable();
            $table->decimal('montant_total', 10, 2)->default(0);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->enum('statut', ['en_attente', 'confirmee', 'en_cours', 'livree', 'annulee'])->default('en_attente');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('approvisionnement_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approvisionnement_id')->constrained('approvisionnements')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->integer('quantite_commande');
            $table->integer('quantite_livree')->default(0);
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('montant_total', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('approvisionnement_produit');
        Schema::dropIfExists('approvisionnements');
    }
}; 