<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Liste des tables qui doivent avoir une superette_id
     */
    protected $tables = [
        'ventes',
        'detail_ventes',
        'clients',
        'caisses',
        'promotions',
        'remises',
        'approvisionnements',
        'detail_approvisionnements',
        'receptions',
        'detail_receptions',
        'retour_ventes',
        'detail_retours',
        'alertes',
        'mouvement_stocks',
        'paiements',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('superette_id')->nullable()->after('id')->constrained('superettes')->nullOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['superette_id']);
                    $table->dropColumn('superette_id');
                });
            }
        }
    }
}; 