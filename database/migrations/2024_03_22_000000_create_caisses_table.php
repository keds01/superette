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
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('nom');
            $table->decimal('solde', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->string('localisation')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Champs pour enregistrer les opérations
            $table->string('type_operation')->nullable(); // 'entree' ou 'sortie'
            $table->decimal('montant', 12, 2)->nullable();
            $table->string('mode_paiement')->nullable();
            $table->text('notes_operation')->nullable(); // Renommé de description pour éviter conflit
            $table->foreignId('vente_id')->nullable()->constrained('ventes')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caisses');
    }
}; 