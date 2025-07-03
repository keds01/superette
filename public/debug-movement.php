<?php
/**
 * Script de diagnostic et de réparation des mouvements de stock orphelins
 */

// Chargement de l'application Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Utilisation de Eloquent directement
use App\Models\MouvementStock;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;

echo "<h1>Diagnostic des mouvements de stock</h1>";

// Récupération du mouvement problématique
$mouvementId = isset($_GET['id']) ? (int)$_GET['id'] : 13;
$mouvement = MouvementStock::find($mouvementId);

if (!$mouvement) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ Le mouvement de stock #{$mouvementId} n'existe pas.";
    echo "</div>";
    exit;
}

echo "<h2>Informations sur le mouvement #{$mouvementId}</h2>";
echo "<ul>";
echo "<li>ID: {$mouvement->id}</li>";
echo "<li>Type: {$mouvement->type}</li>";
echo "<li>Produit ID: {$mouvement->produit_id}</li>";
echo "<li>Quantité avant: {$mouvement->quantite_avant_conditionnement}</li>";
echo "<li>Quantité après: {$mouvement->quantite_apres_conditionnement}</li>";
echo "<li>Date du mouvement: {$mouvement->created_at}</li>";
echo "</ul>";

// Vérification de l'existence du produit (même supprimé)
$produit = Produit::withTrashed()->find($mouvement->produit_id);
echo "<h2>Diagnostic du produit associé</h2>";

if ($produit) {
    echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
    echo "✅ Le produit existe dans la base de données";
    if ($produit->deleted_at) {
        echo " (mais a été supprimé le {$produit->deleted_at})";
    }
    echo "</div>";
    
    echo "<ul>";
    echo "<li>ID: {$produit->id}</li>";
    echo "<li>Nom: {$produit->nom}</li>";
    echo "<li>Catégorie ID: {$produit->categorie_id}</li>";
    echo "</ul>";
} else {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ Le produit #{$mouvement->produit_id} n'existe pas, même parmi les produits supprimés.";
    echo "</div>";
}

// Vérification du chargement de la relation via withTrashed()
echo "<h2>Test du chargement de relation avec withTrashed()</h2>";

$mouvementFrais = MouvementStock::find($mouvementId);
$mouvementFrais->load(['produit' => function($query) {
    $query->withTrashed();
}]);

if ($mouvementFrais->produit) {
    echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
    echo "✅ La relation produit se charge correctement avec withTrashed()";
    echo "</div>";
} else {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ La relation produit ne se charge pas correctement même avec withTrashed()";
    echo "</div>";
}

// Options de correction
echo "<h2>Options de correction</h2>";

if (!$produit) {
    echo "<p>Le produit n'existe plus, vous pouvez :</p>";
    echo "<ul>";
    echo "<li><a href='?id={$mouvementId}&action=delete' style='color: red;'>Supprimer ce mouvement de stock</a></li>";
    echo "</ul>";
} else if ($produit && !$mouvementFrais->produit) {
    echo "<p>Le produit existe mais la relation ne fonctionne pas, vous pouvez :</p>";
    echo "<ul>";
    echo "<li><a href='?id={$mouvementId}&action=fix_relation' style='color: blue;'>Réparer la relation</a></li>";
    echo "</ul>";
}

// Actions de correction
if (isset($_GET['action'])) {
    echo "<h2>Exécution de l'action {$_GET['action']}</h2>";
    
    if ($_GET['action'] === 'delete' && !$produit) {
        $mouvement->delete();
        echo "<div style='background-color: #ffdddd; padding: 10px; border: 1px solid red;'>";
        echo "🗑️ Le mouvement de stock #{$mouvementId} a été supprimé.";
        echo "</div>";
    }
    
    if ($_GET['action'] === 'fix_relation' && $produit) {
        // Tentative de réparation de la relation
        DB::statement("UPDATE mouvements_stock SET produit_id = ? WHERE id = ?", [$produit->id, $mouvementId]);
        echo "<div style='background-color: #ddffdd; padding: 10px; border: 1px solid green;'>";
        echo "🔧 La relation a été réparée. <a href='?id={$mouvementId}'>Rafraîchir pour vérifier</a>";
        echo "</div>";
    }
}

// Lien pour retourner à la liste des mouvements