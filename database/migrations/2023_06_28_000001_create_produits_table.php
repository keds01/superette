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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('reference')->unique();
            $table->string('code_barres')->nullable()->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('categorie_id');
            $table->unsignedBigInteger('unite_vente_id');
            $table->decimal('conditionnement_fournisseur', 10, 2)->default(1);
            $table->decimal('quantite_par_conditionnement', 10, 2)->default(1);
            $table->decimal('stock', 10, 2)->default(0);
            $table->decimal('seuil_alerte', 10, 2)->default(0);
            $table->string('emplacement_rayon')->nullable();
            $table->string('emplacement_etagere')->nullable();
            $table->date('date_peremption')->nullable();
            $table->decimal('prix_achat_ht', 12, 2)->default(0);
            $table->decimal('prix_vente_ht', 12, 2)->default(0);
            $table->decimal('prix_vente_ttc', 12, 2)->default(0);
            $table->decimal('marge', 5, 2)->default(0);
            $table->decimal('tva', 5, 2)->default(18);
            $table->string('image')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('categorie_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('unite_vente_id')->references('id')->on('unites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
