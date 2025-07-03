<?php
/**
 * Script de réparation de la relation entre le mouvement de stock #26 et son produit
 */

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

echo "<h1>Réparation de la relation du mouvement de stock #26</h1>";

// Récupération du mouvement problématique
$mouvementId = 26;
$mouvement = MouvementStock::find($mouvementId);

if (!$mouvement) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ Le mouvement de stock #{$mouvementId} n'existe pas.";
    echo "</div>";
    exit;
}

// Vérification du produit associé
$produit = Produit::withTrashed()->find($mouvement->produit_id);

if (!$produit) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ Le produit #{$mouvement->produit_id} n'existe pas, même parmi les produits supprimés.";
    echo "</div>";
    exit;
}

echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
echo "✅ Le produit existe dans la base de données: {$produit->nom} (ID: {$produit->id})";
echo "</div>";

// Vérification de la relation
$mouvementFrais = MouvementStock::find($mouvementId);
$mouvementFrais->load(['produit' => function($query) {
    $query->withTrashed();
}]);

if ($mouvementFrais->produit) {
    echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
    echo "✅ La relation produit se charge correctement. Aucune réparation nécessaire.";
    echo "</div>";
} else {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ La relation produit ne se charge pas correctement même avec withTrashed()";
    echo "</div>";
    
    // Réparation de la relation
    if (isset($_GET['action']) && $_GET['action'] === 'repair') {
        try {
            // Utilisation directe de DB pour forcer la mise à jour
            DB::statement("UPDATE mouvements_stock SET produit_id = ? WHERE id = ?", [$produit->id, $mouvementId]);
            
            echo "<div style='background-color: #ddffdd; padding: 10px; border: 1px solid green;'>";
            echo "✅ La relation a été réparée.";
            echo "</div>";
            
            // Vérification après réparation
            $mouvementVerif = MouvementStock::find($mouvementId);
            $mouvementVerif->load(['produit' => function($query) {
                $query->withTrashed();
            }]);
            
            if ($mouvementVerif->produit) {
                echo "<div style='background-color: #ddffdd; padding: 10px; border: 1px solid green; margin-top: 10px;'>";
                echo "✅ Vérification réussie: la relation fonctionne maintenant correctement.";
                echo "</div>";
            } else {
                echo "<div style='background-color: #ffdddd; padding: 10px; border: 1px solid red; margin-top: 10px;'>";
                echo "⚠️ La réparation n'a pas fonctionné. La relation est toujours cassée.";
                echo "</div>";
            }
        } catch (\Exception $e) {
            echo "<div style='background-color: #ffdddd; padding: 10px; border: 1px solid red;'>";
            echo "⚠️ Erreur lors de la réparation: " . $e->getMessage();
            echo "</div>";
        }
    } else {
        echo "<p><a href='?action=repair' style='padding: 8px 16px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Réparer la relation</a></p>";
    }
}

// Lien pour retourner à la page du mouvement
echo "<p><a href='/mouvements-stock/{$mouvementId}'>Retourner à la page du mouvement</a></p>";
echo "<p><a href='/mouvements-stock'>Retour à la liste des mouvements</a></p>"; 