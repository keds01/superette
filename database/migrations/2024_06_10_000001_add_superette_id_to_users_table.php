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
        Schema::table('users', function (Blueprint $table) {
            // Le champ est nullable car les Super Admins n'appartiennent à aucune superette
            $table->foreignId('superette_id')->nullable()->after('id')->constrained('superettes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['superette_id']);
            $table->dropColumn('superette_id');
        });
    }
}; 