<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'eloyisfrx@gmail.com'],
            [
                'name' => 'eloyis',
                'password' => Hash::make('eloyisfrx@gmail.com'),
                'role' => User::ROLE_SUPER_ADMIN,
                'actif' => true,
                'telephone' => '0000000000',
                'adresse' => 'Siège',
            ]
        );
    }
} 