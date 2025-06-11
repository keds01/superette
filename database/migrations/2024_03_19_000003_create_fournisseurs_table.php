<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->string('contact_principal');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->default('Sénégal');
            $table->string('ninea')->nullable()->unique(); // Numéro d'identification fiscale
            $table->string('registre_commerce')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('solde_actuel', 15, 2)->default(0);
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pour les contacts supplémentaires des fournisseurs
        Schema::create('contacts_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('fonction');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->boolean('contact_principal')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Table pour les informations de paiement des fournisseurs
        Schema::create('paiements_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained()->onDelete('cascade');
            $table->string('banque')->nullable();
            $table->string('numero_compte')->nullable();
            $table->string('titulaire_compte')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Table pour l'évaluation des fournisseurs
        Schema::create('evaluations_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date_evaluation');
            $table->integer('qualite_produits')->comment('Note sur 10');
            $table->integer('delai_livraison')->comment('Note sur 10');
            $table->integer('prix_competitifs')->comment('Note sur 10');
            $table->integer('service_client')->comment('Note sur 10');
            $table->text('commentaires')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations_fournisseurs');
        Schema::dropIfExists('paiements_fournisseurs');
        Schema::dropIfExists('contacts_fournisseurs');
        Schema::dropIfExists('fournisseurs');
    }
}; 