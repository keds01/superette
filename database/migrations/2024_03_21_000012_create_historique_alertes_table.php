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
        Schema::create('historique_alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alerte_id')->constrained('alertes')->onDelete('cascade');
            $table->string('message');
            $table->boolean('resolue')->default(false);
            $table->timestamp('date_resolution')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_alertes');
    }
}; 