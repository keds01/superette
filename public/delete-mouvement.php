<?php
/**
 * Script de suppression du mouvement de stock #2
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
use Illuminate\Support\Facades\DB;

echo "<h1>Suppression du mouvement de stock #2</h1>";

// Récupération du mouvement à supprimer
$mouvementId = isset($_GET['id']) ? (int)$_GET['id'] : 2;
$mouvement = MouvementStock::find($mouvementId);

if (!$mouvement) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ Le mouvement de stock #{$mouvementId} n'existe pas.";
    echo "</div>";
    exit;
}

// Affichage des informations du mouvement
echo "<h2>Informations sur le mouvement à supprimer</h2>";
echo "<ul>";
echo "<li>ID: {$mouvement->id}</li>";
echo "<li>Type: {$mouvement->type}</li>";
echo "<li>Produit ID: {$mouvement->produit_id}</li>";
echo "<li>Quantité avant: {$mouvement->quantite_avant_conditionnement}</li>";
echo "<li>Quantité après: {$mouvement->quantite_apres_conditionnement}</li>";
echo "<li>Date du mouvement: " . ($mouvement->date_mouvement ?? $mouvement->created_at) . "</li>";
echo "</ul>";

// Confirmation de suppression
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    try {
        DB::beginTransaction();
        
        // Suppression du mouvement
        $mouvement->delete();
        
        DB::commit();
        
        echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
        echo "✅ Le mouvement de stock #{$mouvementId} a été supprimé avec succès.";
        echo "</div>";
        
        echo "<p><a href='/mouvements-stock'>Retour à la liste des mouvements</a></p>";
    } catch (\Exception $e) {
        DB::rollBack();
        
        echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
        echo "⚠️ Erreur lors de la suppression du mouvement: " . $e->getMessage();
        echo "</div>";
    }
} else {
    // Demande de confirmation
    echo "<div style='color: orange; padding: 10px; border: 1px solid orange;'>";
    echo "⚠️ Êtes-vous sûr de vouloir supprimer ce mouvement de stock ?";
    echo "</div>";
    
    echo "<p>";
    echo "<a href='?id={$mouvementId}&confirm=yes' style='background-color: #f44336; color: white; padding: 10px 15px; text-decoration: none; margin-right: 10px;'>Oui, supprimer</a>";
    echo "<a href='/mouvements-stock' style='background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none;'>Non, annuler</a>";
    echo "</p>";
} 