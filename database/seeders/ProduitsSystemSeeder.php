<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Unite;
use Carbon\Carbon;

class ProduitsSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les catégories et unités existantes
        $categories = Categorie::all();
        $unites = Unite::all();

        if ($categories->isEmpty() || $unites->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter les seeders pour les catégories et les unités.');
            return;
        }

        // Utiliser la première catégorie et unité disponibles
        $categorie = $categories->first();
        $unite = $unites->first();

        // Générer un timestamp unique pour les références
        $timestamp = time();

        $produits = [
            [
                'nom' => 'Riz Basmati',
                'reference' => 'RIZ-BAS-1KG-' . $timestamp,
                'code_barres' => '1234567890123-' . $timestamp,
                'description' => 'Riz Basmati de qualité supérieure, 1kg',
                'categorie_id' => $categorie->id,
                'unite_vente_id' => $unite->id,
                'conditionnement_fournisseur' => 10,
                'quantite_par_conditionnement' => 1,
                'stock' => 50,
                'seuil_alerte' => 10,
                'emplacement_rayon' => 'A',
                'emplacement_etagere' => '1',
                'date_peremption' => Carbon::now()->addMonths(12),
                'prix_achat_ht' => 1500,
                'prix_vente_ht' => 2000,
                'marge' => 25,
                'tva' => 18,
                'actif' => true
            ],
            [
                'nom' => 'Huile d\'olive extra vierge',
                'reference' => 'HUILE-OLIVE-1L-' . $timestamp,
                'code_barres' => '1234567890124-' . $timestamp,
                'description' => 'Huile d\'olive extra vierge, 1 litre',
                'categorie_id' => $categorie->id,
                'unite_vente_id' => $unite->id,
                'conditionnement_fournisseur' => 12,
                'quantite_par_conditionnement' => 1,
                'stock' => 30,
                'seuil_alerte' => 5,
                'emplacement_rayon' => 'B',
                'emplacement_etagere' => '2',
                'date_peremption' => Carbon::now()->addMonths(24),
                'prix_achat_ht' => 3500,
                'prix_vente_ht' => 4500,
                'marge' => 28.57,
                'tva' => 18,
                'actif' => true
            ],
            [
                'nom' => 'Pâtes Spaghetti',
                'reference' => 'PATES-SPAG-500G-' . $timestamp,
                'code_barres' => '1234567890125-' . $timestamp,
                'description' => 'Pâtes Spaghetti de qualité, 500g',
                'categorie_id' => $categorie->id,
                'unite_vente_id' => $unite->id,
                'conditionnement_fournisseur' => 20,
                'quantite_par_conditionnement' => 0.5,
                'stock' => 100,
                'seuil_alerte' => 20,
                'emplacement_rayon' => 'A',
                'emplacement_etagere' => '3',
                'date_peremption' => Carbon::now()->addMonths(18),
                'prix_achat_ht' => 800,
                'prix_vente_ht' => 1200,
                'marge' => 33.33,
                'tva' => 18,
                'actif' => true
            ]
        ];

        foreach ($produits as $produit) {
            // Calculer le prix de vente TTC
            $produit['prix_vente_ttc'] = $produit['prix_vente_ht'] * (1 + $produit['tva'] / 100);
            
            Produit::create($produit);
        }

        $this->command->info('Produits créés avec succès !');
    }
} 