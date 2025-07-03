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
        Schema::table('remises', function (Blueprint $table) {
            // Renommer le champ 'montant' en 'montant_remise' pour plus de clarté
            $table->renameColumn('montant', 'montant_remise');
            
            // Ajouter les nouveaux champs
            $table->string('code_remise')->nullable()->unique()->after('vente_id');
            $table->enum('type_remise', ['pourcentage', 'montant_fixe'])->default('pourcentage')->after('code_remise');
            $table->decimal('valeur_remise', 10, 2)->after('type_remise');
            $table->text('description')->nullable()->after('montant_remise');
            $table->boolean('actif')->default(true)->after('description');
            
            // Supprimer ou renommer le champ 'motif' car il est remplacé par 'description'
            $table->dropColumn('motif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remises', function (Blueprint $table) {
            // Restaurer l'ancien schéma
            $table->renameColumn('montant_remise', 'montant');
            $table->string('motif')->nullable();
            
            // Supprimer les nouveaux champs
            $table->dropColumn([
                'code_remise',
                'type_remise',
                'valeur_remise',
                'description',
                'actif'
            ]);
        });
    }
};
