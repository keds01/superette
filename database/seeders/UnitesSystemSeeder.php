<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitesSystemSeeder extends Seeder
{
    public function run(): void
    {
        // On protège l'idempotence : pas de doublons sur les symboles système
        $unites = [
            ['nom' => 'kilogramme', 'symbole' => 'kg', 'description' => 'Poids', 'actif' => true, 'system' => true],
            ['nom' => 'gramme', 'symbole' => 'g', 'description' => 'Poids', 'actif' => true, 'system' => true],
            ['nom' => 'litre', 'symbole' => 'L', 'description' => 'Volume', 'actif' => true, 'system' => true],
            ['nom' => 'centilitre', 'symbole' => 'cl', 'description' => 'Volume', 'actif' => true, 'system' => true],
            ['nom' => 'millilitre', 'symbole' => 'ml', 'description' => 'Volume', 'actif' => true, 'system' => true],
            ['nom' => 'pièce', 'symbole' => 'pc', 'description' => 'Unité', 'actif' => true, 'system' => true],
            ['nom' => 'paquet', 'symbole' => 'paq', 'description' => 'Lot', 'actif' => true, 'system' => true],
            ['nom' => 'boîte', 'symbole' => 'bt', 'description' => 'Contenant', 'actif' => true, 'system' => true],
            ['nom' => 'douzaine', 'symbole' => 'dz', 'description' => 'Lot de 12', 'actif' => true, 'system' => true],
            ['nom' => 'mètre', 'symbole' => 'm', 'description' => 'Longueur', 'actif' => true, 'system' => true],
            ['nom' => 'centimètre', 'symbole' => 'cm', 'description' => 'Longueur', 'actif' => true, 'system' => true],
            ['nom' => 'millimètre', 'symbole' => 'mm', 'description' => 'Longueur', 'actif' => true, 'system' => true],
            ['nom' => 'sachet', 'symbole' => 'sachet', 'description' => 'Emballage', 'actif' => true, 'system' => true],
            ['nom' => 'lot', 'symbole' => 'lot', 'description' => 'Lot', 'actif' => true, 'system' => true],
            ['nom' => 'barquette', 'symbole' => 'bqt', 'description' => 'Contenant', 'actif' => true, 'system' => true],
            ['nom' => 'bidon', 'symbole' => 'bid', 'description' => 'Contenant liquide', 'actif' => true, 'system' => true],
            ['nom' => 'fût', 'symbole' => 'fût', 'description' => 'Contenant', 'actif' => true, 'system' => true],
            ['nom' => 'carton', 'symbole' => 'ctn', 'description' => 'Emballage', 'actif' => true, 'system' => true],
            ['nom' => 'palette', 'symbole' => 'plt', 'description' => 'Palette logistique', 'actif' => true, 'system' => true],
            ['nom' => 'bouteille', 'symbole' => 'btle', 'description' => 'Contenant liquide', 'actif' => true, 'system' => true],
            ['nom' => 'tablette', 'symbole' => 'tblt', 'description' => 'Tablette ou barre', 'actif' => true, 'system' => true],
            ['nom' => 'pot', 'symbole' => 'pot', 'description' => 'Pot', 'actif' => true, 'system' => true],
            ['nom' => 'seau', 'symbole' => 'seau', 'description' => 'Seau', 'actif' => true, 'system' => true],
            ['nom' => 'rouleau', 'symbole' => 'rl', 'description' => 'Rouleau', 'actif' => true, 'system' => true],
            ['nom' => 'tranche', 'symbole' => 'tr', 'description' => 'Tranche', 'actif' => true, 'system' => true],
            ['nom' => 'bouquet', 'symbole' => 'bqt', 'description' => 'Bouquet', 'actif' => true, 'system' => true],
            ['nom' => 'filet', 'symbole' => 'flt', 'description' => 'Filet', 'actif' => true, 'system' => true],
        ];

        foreach ($unites as $unite) {
            DB::table('unites')->updateOrInsert(
                ['symbole' => $unite['symbole'], 'system' => true], // Conditions pour la recherche
                array_merge($unite, ['created_at' => now(), 'updated_at' => now()]) // Valeurs à insérer/mettre à jour
            );
        }
    }
}
