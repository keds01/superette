<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Superette;

class SuperetteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer une superette par défaut si elle n'existe pas déjà
        Superette::firstOrCreate(
            ['code' => 'SP001'],
            [
                'nom' => 'SUPERETTE PRINCIPALE',
                'adresse' => null,
                'telephone' => null,
                'email' => 'contact@superette.com',
                'description' => 'Superette principale du système',
                'actif' => true,
            ]
        );
    }
} 