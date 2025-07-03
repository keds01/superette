<?php
/**
 * Script de recréation du mouvement de stock #2
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

echo "<h1>Recréation du mouvement de stock</h1>";

// Vérifier si le produit CAGE existe
$produit = Produit::withTrashed()->find(8);

if (!$produit) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ Le produit CAGE (ID: 8) n'existe pas.";
    echo "</div>";
    exit;
}

echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
echo "✅ Le produit CAGE (ID: 8) existe.";
echo "</div>";

// Confirmation de recréation
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    try {
        DB::beginTransaction();
        
        // Création d'un nouveau mouvement de stock
        $mouvement = new MouvementStock();
        $mouvement->produit_id = 8; // ID du produit CAGE
        $mouvement->type = 'entree';
        $mouvement->quantite_avant_conditionnement = 0;
        $mouvement->quantite_apres_conditionnement = 100;
        $mouvement->quantite_avant_unite = 0;
        $mouvement->quantite_apres_unite = 100;
        $mouvement->motif = 'Recréation du mouvement de stock';
        $mouvement->date_mouvement = now();
        $mouvement->save();
        
        DB::commit();
        
        echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
        echo "✅ Un nouveau mouvement de stock a été créé avec l'ID #{$mouvement->id}.";
        echo "</div>";
        
        echo "<p><a href='/mouvements-stock/{$mouvement->id}' style='background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none;'>Voir le mouvement créé</a></p>";
        echo "<p><a href='/mouvements-stock'>Retour à la liste des mouvements</a></p>";
    } catch (\Exception $e) {
        DB::rollBack();
        
        echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
        echo "⚠️ Erreur lors de la création du mouvement: " . $e->getMessage();
        echo "</div>";
    }
} else {
    // Demande de confirmation
    echo "<div style='color: blue; padding: 10px; border: 1px solid blue;'>";
    echo "ℹ️ Vous êtes sur le point de créer un nouveau mouvement de stock pour le produit CAGE.";
    echo "</div>";
    
    echo "<h2>Détails du mouvement à créer</h2>";
    echo "<ul>";
    echo "<li>Produit: CAGE (ID: 8)</li>";
    echo "<li>Type: Entrée</li>";
    echo "<li>Quantité avant: 0</li>";
    echo "<li>Quantité après: 100</li>";
    echo "<li>Motif: Recréation du mouvement de stock</li>";
    echo "<li>Date: " . now()->format('Y-m-d H:i:s') . "</li>";
    echo "</ul>";
    
    echo "<p>";
    echo "<a href='?confirm=yes' style='background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; margin-right: 10px;'>Créer le mouvement</a>";
    echo "<a href='/mouvements-stock' style='background-color: #f44336; color: white; padding: 10px 15px; text-decoration: none;'>Annuler</a>";
    echo "</p>";
} 