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
        Schema::table('detail_ventes', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_ventes', 'prix_achat_unitaire')) {
                $table->decimal('prix_achat_unitaire', 10, 2)->after('prix_unitaire')->default(0.00)->comment('Prix d\'achat unitaire HT au moment de la vente');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression du dropColumn pour garantir la compatibilité totale lors des refresh
    }
};
