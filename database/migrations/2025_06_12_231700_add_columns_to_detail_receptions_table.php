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
        Schema::table('detail_receptions', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_receptions', 'reception_id')) {
                $table->foreignId('reception_id')->nullable()->constrained('receptions')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('detail_receptions', 'produit_id')) {
                $table->foreignId('produit_id')->nullable()->constrained('produits')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('detail_receptions', 'quantite')) {
                $table->decimal('quantite', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('detail_receptions', 'prix_unitaire')) {
                $table->decimal('prix_unitaire', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('detail_receptions', 'date_peremption')) {
                $table->date('date_peremption')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_receptions', function (Blueprint $table) {
            if (Schema::hasColumn('detail_receptions', 'reception_id')) {
                $table->dropForeign(['reception_id']);
                $table->dropColumn('reception_id');
            }
            if (Schema::hasColumn('detail_receptions', 'produit_id')) {
                $table->dropForeign(['produit_id']);
                $table->dropColumn('produit_id');
            }
            if (Schema::hasColumn('detail_receptions', 'quantite')) {
                $table->dropColumn('quantite');
            }
            if (Schema::hasColumn('detail_receptions', 'prix_unitaire')) {
                $table->dropColumn('prix_unitaire');
            }
            if (Schema::hasColumn('detail_receptions', 'date_peremption')) {
                $table->dropColumn('date_peremption');
            }
        });
    }
};
