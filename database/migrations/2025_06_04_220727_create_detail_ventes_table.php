<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detail_ventes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('produits');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 12, 2);
            $table->decimal('sous_total', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_ventes');
    }
};
