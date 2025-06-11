<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained('ventes')->onDelete('cascade');
            $table->string('mode_paiement');
            $table->decimal('montant', 10, 2);
            $table->string('reference_paiement')->nullable();
            $table->string('statut')->default('valide');
            $table->text('notes')->nullable();
            $table->dateTime('date_paiement')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiements');
    }
}; 