<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserWithRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un super admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
            'actif' => true,
        ]);
        
        // Créer un admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'actif' => true,
        ]);
        
        // Créer un vendeur
        User::create([
            'name' => 'Vendeur',
            'email' => 'vendeur@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_VENDEUR,
            'actif' => true,
        ]);
        
        // Mettre à jour un utilisateur existant si présent
        $existingUser = User::where('email', 'test@example.com')->first();
        if ($existingUser) {
            $existingUser->role = User::ROLE_SUPER_ADMIN;
            $existingUser->save();
            $this->command->info('Utilisateur existant "test@example.com" mis à jour avec le rôle super_admin');
        }
    }
}
