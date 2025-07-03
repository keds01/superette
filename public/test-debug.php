<?php
// Chargement de l'application Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Utilisation de Eloquent et DB
use App\Models\MouvementStock;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;

echo "<h1>Test du mouvement de stock #26</h1>";

// Récupération du mouvement
$mouvement = MouvementStock::find(26);

if (!$mouvement) {
    echo "Le mouvement n'existe pas.";
    exit;
}

echo "<h2>Données du mouvement</h2>";
echo "<pre>";
var_dump($mouvement->toArray());
echo "</pre>";

// Test de la relation
echo "<h2>Test de la relation</h2>";
$mouvement->load('produit');
echo "Produit chargé: " . ($mouvement->produit ? "OUI" : "NON") . "<br>";

// Récupération manuelle
$produit = Produit::withTrashed()->find($mouvement->produit_id);
echo "Produit trouvé manuellement: " . ($produit ? "OUI" : "NON") . "<br>";

if ($produit) {
    echo "<h2>Données du produit</h2>";
    echo "<pre>";
    var_dump($produit->toArray());
    echo "</pre>";
}

// Réparation
echo "<h2>Réparation</h2>";
if (isset($_GET['action']) && $_GET['action'] === 'fix') {
    DB::statement("UPDATE mouvements_stock SET produit_id = ? WHERE id = ?", [$mouvement->produit_id, $mouvement->id]);
    echo "Réparation effectuée. <a href='/mouvements-stock/26'>Vérifier</a>";
} else {
    echo "<a href='?action=fix'>Réparer la relation</a>";
} 