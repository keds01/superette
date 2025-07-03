<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer un utilisateur caissier de test
        DB::table('users')->insert([
            'name' => 'Caissier Test',
            'email' => 'caissier@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CAISSIER,
            'actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer l'utilisateur caissier de test
        DB::table('users')->where('email', 'caissier@example.com')->delete();
    }
}; 