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
            $table->decimal('prix_achat_unitaire', 12, 2)->after('prix_unitaire')->default(0);
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
