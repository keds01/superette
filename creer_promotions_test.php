<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;

// Vérification si des produits existent
$produits = Product::take(5)->get();

if ($produits->isEmpty()) {
    echo "ERREUR: Aucun produit trouvé. Impossible de créer des promotions sans produits.\n";
    echo "Veuillez d'abord créer des produits.\n";
    exit(1);
}

// Supprimer les anciennes promotions
Promotion::query()->delete();
echo "Anciennes promotions supprimées.\n";

// Créer des promotions de test
try {
    DB::beginTransaction();
    
    foreach ($produits as $index => $produit) {
        $typePromo = ($index % 2 == 0) ? 'pourcentage' : 'montant';
        $valeur = ($typePromo == 'pourcentage') ? rand(5, 30) : rand(100, 1000);
        
        Promotion::create([
            'product_id' => $produit->id,
            'type' => $typePromo,
            'valeur' => $valeur,
            'date_debut' => now(),
            'date_fin' => now()->addDays(30),
            'description' => 'Promotion test ' . ($index + 1),
            'actif' => true
        ]);
        
        echo "Promotion créée pour le produit: {$produit->nom} ({$typePromo}: {$valeur})\n";
    }
    
    DB::commit();
    echo "\nCRÉATION RÉUSSIE: " . count($produits) . " promotions ont été créées avec succès.\n";
    echo "Rendez-vous sur la page Promotions pour voir les résultats.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERREUR: " . $e->getMessage() . "\n";
}
