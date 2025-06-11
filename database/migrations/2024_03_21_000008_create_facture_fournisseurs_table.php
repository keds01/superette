<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('facture_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            $table->foreignId('approvisionnement_id')->nullable()->constrained('approvisionnements')->onDelete('set null');
            $table->string('numero_facture')->unique();
            $table->date('date_facture');
            $table->date('date_echeance');
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('montant_tva', 10, 2);
            $table->decimal('montant_ttc', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->decimal('montant_restant', 10, 2);
            $table->enum('statut', ['en_attente', 'partiellement_paye', 'paye', 'annulee'])->default('en_attente');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('facture_fournisseurs');
    }
}; 