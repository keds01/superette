<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la colonne existe déjà
        if (!Schema::hasColumn('mouvements_stock', 'superette_id')) {
            Schema::table('mouvements_stock', function (Blueprint $table) {
                $table->foreignId('superette_id')->nullable()->constrained('superettes')->onDelete('set null');
            });
            
            // Associer tous les mouvements existants à la première superette
            if (Schema::hasTable('superettes') && DB::table('superettes')->count() > 0) {
                $defaultSuperetteId = DB::table('superettes')->first()->id;
                DB::table('mouvements_stock')->whereNull('superette_id')->update(['superette_id' => $defaultSuperetteId]);
            }
            
            // Log pour migrator
            DB::statement('SELECT "Migration: Added superette_id column to mouvements_stock table" as "Migration Log"');
        } else {
            // Log pour migrator
            DB::statement('SELECT "Migration: superette_id column already exists in mouvements_stock table" as "Migration Log"');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('mouvements_stock', 'superette_id')) {
            Schema::table('mouvements_stock', function (Blueprint $table) {
                $table->dropForeign(['superette_id']);
                $table->dropColumn('superette_id');
            });
        }
    }
}; 