<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_vente')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employe_id')->constrained('employes');
            $table->dateTime('date_vente');
            $table->enum('type_vente', ['sur_place', 'a_emporter', 'livraison'])->default('sur_place');
            $table->decimal('montant_total', 12, 2);
            $table->decimal('montant_paye', 12, 2)->default(0);
            $table->decimal('montant_restant', 12, 2)->default(0);
            $table->string('statut')->default('en_cours');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ventes');
    }
};
