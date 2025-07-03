<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('poste')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->string('adresse')->nullable();
            $table->date('date_embauche')->nullable();
            $table->decimal('salaire', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('documents_employes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->string('type'); // cv, contrat, diplome, certificat, autre
            $table->string('titre');
            $table->string('fichier');
            $table->date('date_document');
            $table->date('date_expiration')->nullable();
            $table->boolean('est_confidentiel')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents_employes');
        Schema::dropIfExists('employes');
    }
}; 