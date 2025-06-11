<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Pour les slugs

class CategoriesSystemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Fruits et Légumes', 'description' => 'Produits frais du marché'],
            ['nom' => 'Boucherie et Charcuterie', 'description' => 'Viandes fraîches et préparations'],
            ['nom' => 'Poissonnerie', 'description' => 'Poissons et fruits de mer'],
            ['nom' => 'Produits Laitiers et Fromages', 'description' => 'Lait, yaourts, fromages, crèmes'],
            ['nom' => 'Boulangerie et Pâtisserie', 'description' => 'Pains, viennoiseries, gâteaux'],
            ['nom' => 'Épicerie Salée', 'description' => 'Pâtes, riz, conserves, huiles, condiments'],
            ['nom' => 'Épicerie Sucrée', 'description' => 'Biscuits, chocolat, confitures, café, thé'],
            ['nom' => 'Boissons', 'description' => 'Eau, jus, sodas, sirops, alcools'],
            ['nom' => 'Surgelés', 'description' => 'Plats préparés, légumes, viandes, glaces surgelés'],
            ['nom' => 'Hygiène et Beauté', 'description' => 'Soins corporels, capillaires, maquillage'],
            ['nom' => 'Entretien Ménager', 'description' => 'Produits de nettoyage, lessives'],
            ['nom' => 'Bébé et Puériculture', 'description' => 'Alimentation, couches, soins pour bébés'],
            ['nom' => 'Animaux', 'description' => 'Alimentation et accessoires pour animaux'],
        ];

        foreach ($categories as $categorie) {
            DB::table('categories')->updateOrInsert(
                ['nom' => $categorie['nom'], 'system' => true], // Clé unique pour updateOrInsert
                [
                    'nom' => $categorie['nom'],
                    'slug' => Str::slug($categorie['nom']), // Génération du slug
                    'description' => $categorie['description'],
                    'parent_id' => null, // Catégories de premier niveau par défaut
                    'actif' => true,
                    'system' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
