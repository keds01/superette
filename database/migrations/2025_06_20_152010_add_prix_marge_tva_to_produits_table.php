<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cette migration est désactivée car elle fait double emploi avec
     * 2025_06_20_151946_add_prix_marge_tva_to_produits_table.php
     */
    public function up(): void
    {
        // Migration désactivée - fonctionnalité déjà implémentée
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migration désactivée - rien à annuler
    }
};
