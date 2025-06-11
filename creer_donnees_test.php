<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Categorie;
use App\Models\Promotion;
use App\Models\Unite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "=== CRÉATION DE DONNÉES DE TEST POUR L'APPLICATION ===\n\n";

try {
    DB::beginTransaction();

    // 1. Création d'une catégorie de test si aucune n'existe
    $categorieCount = Categorie::count();
    if ($categorieCount == 0) {
        $categories = [
            ['nom' => 'Boissons', 'description' => 'Boissons gazeuses, jus, eau'],
            ['nom' => 'Alimentation', 'description' => 'Produits alimentaires'],
            ['nom' => 'Hygiène', 'description' => 'Produits d\'hygiène personnelle'],
        ];

        foreach ($categories as $categorie) {
            Categorie::create([
                'nom' => $categorie['nom'],
                'description' => $categorie['description']
            ]);
        }
        echo "✓ " . count($categories) . " catégories créées\n";
    } else {
        echo "✓ " . $categorieCount . " catégories existantes\n";
    }

    // 2. Création d'unités de test si aucune n'existe
    $uniteCount = Unite::count();
    if ($uniteCount == 0) {
        $unites = [
            ['nom' => 'Pièce', 'symbole' => 'pc'],
            ['nom' => 'Kilogramme', 'symbole' => 'kg'],
            ['nom' => 'Litre', 'symbole' => 'L'],
        ];

        foreach ($unites as $unite) {
            Unite::create([
                'nom' => $unite['nom'],
                'symbole' => $unite['symbole']
            ]);
        }
        echo "✓ " . count($unites) . " unités créées\n";
    } else {
        echo "✓ " . $uniteCount . " unités existantes\n";
    }

    // 3. Création de produits de test
    $productCount = Product::count();
    if ($productCount == 0) {
        $produits = [
            [
                'nom' => 'Coca-Cola 1.5L',
                'reference' => 'COCA-1.5L',
                'description' => 'Bouteille de Coca-Cola 1.5L',
                'prix_achat' => 500,
                'prix_vente' => 850,
                'category_id' => 1,
                'unit_id' => 1,
                'stock_minimum' => 10,
                'stock_actuel' => 45
            ],
            [
                'nom' => 'Riz Uncle Benz 5kg',
                'reference' => 'RIZ-UB-5KG',
                'description' => 'Paquet de riz Uncle Benz 5kg',
                'prix_achat' => 2500,
                'prix_vente' => 3200,
                'category_id' => 2,
                'unit_id' => 1,
                'stock_minimum' => 5,
                'stock_actuel' => 12
            ],
            [
                'nom' => 'Savon Dove',
                'reference' => 'SAVON-DOVE',
                'description' => 'Savon Dove pour le corps',
                'prix_achat' => 300,
                'prix_vente' => 550,
                'category_id' => 3,
                'unit_id' => 1,
                'stock_minimum' => 15,
                'stock_actuel' => 30
            ],
            [
                'nom' => 'Lait Candia 1L',
                'reference' => 'LAIT-CANDIA-1L',
                'description' => 'Bouteille de lait Candia 1L',
                'prix_achat' => 700,
                'prix_vente' => 1000,
                'category_id' => 2,
                'unit_id' => 3,
                'stock_minimum' => 8,
                'stock_actuel' => 20
            ],
            [
                'nom' => 'Dentifrice Signal',
                'reference' => 'DENT-SIGNAL',
                'description' => 'Dentifrice Signal protection complète',
                'prix_achat' => 450,
                'prix_vente' => 750,
                'category_id' => 3,
                'unit_id' => 1,
                'stock_minimum' => 10,
                'stock_actuel' => 25
            ],
        ];

        foreach ($produits as $produit) {
            Product::create($produit);
        }
        echo "✓ " . count($produits) . " produits créés\n";
    } else {
        echo "✓ " . $productCount . " produits existants\n";
    }

    // 4. Création des promotions
    $promotionCount = Promotion::count();
    if ($promotionCount == 0) {
        // Récupérer tous les produits
        $produits = Product::all();
        $promotions = [];

        foreach ($produits as $index => $produit) {
            if ($index < 3) { // Créer des promotions seulement pour les 3 premiers produits
                $typePromo = ($index % 2 == 0) ? 'pourcentage' : 'montant';
                $valeur = ($typePromo == 'pourcentage') ? rand(5, 30) : rand(100, 500);
                
                $promotion = Promotion::create([
                    'product_id' => $produit->id,
                    'type' => $typePromo,
                    'valeur' => $valeur,
                    'date_debut' => now(),
                    'date_fin' => now()->addDays(30),
                    'description' => 'Promotion sur ' . $produit->nom,
                    'actif' => true
                ]);
                
                $promotions[] = $promotion;
            }
        }
        echo "✓ " . count($promotions) . " promotions créées\n";
    } else {
        echo "✓ " . $promotionCount . " promotions existantes\n";
    }

    DB::commit();
    echo "\n=== DONNÉES CRÉÉES AVEC SUCCÈS ===\n";
    echo "Vous pouvez maintenant accéder à votre application et voir les promotions.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
