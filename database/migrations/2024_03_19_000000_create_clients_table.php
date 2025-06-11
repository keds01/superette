<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->default('Sénégal');
            $table->enum('type', ['particulier', 'entreprise'])->default('particulier');
            $table->string('ninea')->nullable()->unique(); // Pour les entreprises
            $table->string('registre_commerce')->nullable(); // Pour les entreprises
            $table->decimal('solde_actuel', 15, 2)->default(0);
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pour les contacts des clients (entreprises)
        Schema::create('contacts_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('prenom');
            $table->string('fonction');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->boolean('contact_principal')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts_clients');
        Schema::dropIfExists('clients');
    }
}; 