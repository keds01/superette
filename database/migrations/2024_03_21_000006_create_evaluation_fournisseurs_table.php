<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluation_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date_evaluation');
            $table->integer('qualite_produits')->comment('Note sur 10');
            $table->integer('delai_livraison')->comment('Note sur 10');
            $table->integer('prix_competitifs')->comment('Note sur 10');
            $table->integer('service_client')->comment('Note sur 10');
            $table->text('commentaire')->nullable();
            $table->text('recommandation')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_fournisseurs');
    }
}; 