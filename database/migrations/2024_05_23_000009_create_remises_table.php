<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('remises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained('ventes')->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->string('motif')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('remises');
    }
}; 