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
        Schema::table('alertes', function (Blueprint $table) {
            if (!Schema::hasColumn('alertes', 'actif')) {
                $table->boolean('actif')->default(true)->after('message');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            if (Schema::hasColumn('alertes', 'actif')) {
                $table->dropColumn('actif');
            }
        });
    }
};
