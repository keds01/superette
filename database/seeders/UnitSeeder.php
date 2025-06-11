<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $units = [
            [
                'nom' => 'Kilogramme',
                'symbole' => 'kg',
                'description' => 'Unité de masse égale à 1000 grammes',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Gramme',
                'symbole' => 'g',
                'description' => 'Unité de masse de base',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Litre',
                'symbole' => 'L',
                'description' => 'Unité de volume égale à 1000 millilitres',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Millilitre',
                'symbole' => 'ml',
                'description' => 'Unité de volume égale à 0.001 litre',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Pièce',
                'symbole' => 'pcs',
                'description' => 'Unité de comptage',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Paquet',
                'symbole' => 'pqt',
                'description' => 'Unité de conditionnement',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Boîte',
                'symbole' => 'bt',
                'description' => 'Unité de conditionnement en boîte',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Carton',
                'symbole' => 'ct',
                'description' => 'Unité de conditionnement en carton',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('units')->insert($units);
    }
} 