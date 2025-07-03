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
        Schema::table('produits', function (Blueprint $table) {
            // Vérifier si la colonne marge n'existe pas avant de l'ajouter
            if (!Schema::hasColumn('produits', 'marge')) {
                $table->decimal('marge', 10, 2)->nullable()->after('prix_vente_ttc');
            }
            
            // Vérifier si la colonne tva n'existe pas avant de l'ajouter
            if (!Schema::hasColumn('produits', 'tva')) {
                $table->decimal('tva', 10, 2)->nullable()->after('marge');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            // Ne pas supprimer les colonnes si elles existaient déjà avant cette migration
            if (Schema::hasColumn('produits', 'marge') && Schema::hasColumn('produits', 'tva')) {
                $table->dropColumn(['marge', 'tva']);
            } else if (Schema::hasColumn('produits', 'marge')) {
                $table->dropColumn('marge');
            } else if (Schema::hasColumn('produits', 'tva')) {
                $table->dropColumn('tva');
            }
        });
    }
};