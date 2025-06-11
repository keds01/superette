<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unites', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('symbole')->unique();
            $table->string('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->boolean('system')->default(false); // Unité système protégée
            $table->timestamps();
            $table->softDeletes();
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('unites');
    }
};