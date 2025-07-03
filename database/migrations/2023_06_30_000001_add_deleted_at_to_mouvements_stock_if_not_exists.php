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
        $columnExists = Schema::hasColumn('mouvements_stock', 'deleted_at');
        
        if (!$columnExists) {
            Schema::table('mouvements_stock', function (Blueprint $table) {
                $table->softDeletes();
            });
            
            // Log pour migrator
            DB::statement('SELECT "Migration: Added deleted_at column to mouvements_stock table" as "Migration Log"');
        } else {
            // Log pour migrator
            DB::statement('SELECT "Migration: deleted_at column already exists in mouvements_stock table" as "Migration Log"');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mouvements_stock', function (Blueprint $table) {
            // Ne pas supprimer si c'était déjà là
            // Cette migration est non destructive
        });
    }
}; 