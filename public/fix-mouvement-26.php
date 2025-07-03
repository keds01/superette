<?php
/**
 * Script de diagnostic et de réparation du mouvement de stock #26
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

echo "<h1>Diagnostic et réparation du mouvement de stock #26</h1>";

// Récupération du mouvement problématique
$mouvementId = 26;
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
    
    // Proposer des solutions
    echo "<h3>Solutions possibles</h3>";
    echo "<ol>";
    echo "<li><a href='?action=delete_mouvement' style='color: red;'>Supprimer ce mouvement de stock</a> (recommandé si le produit n'existe plus)</li>";
    echo "<li><a href='?action=reassign_mouvement' style='color: blue;'>Réassigner ce mouvement à un produit existant</a> (nécessite de choisir un produit)</li>";
    echo "</ol>";
    
    // Traitement des actions
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'delete_mouvement') {
            $mouvement->delete();
            echo "<div style='background-color: #ffdddd; padding: 10px; border: 1px solid red;'>";
            echo "🗑️ Le mouvement de stock #{$mouvementId} a été supprimé avec succès.";
            echo "</div>";
            echo "<p><a href='/mouvements-stock'>Retour à la liste des mouvements</a></p>";
            exit;
        }
        else if ($_GET['action'] === 'reassign_mouvement') {
            // Afficher la liste des produits disponibles
            $produits = Produit::orderBy('nom')->get();
            echo "<h3>Choisir un produit pour réassigner le mouvement</h3>";
            echo "<form action='' method='GET'>";
            echo "<input type='hidden' name='action' value='do_reassign'>";
            echo "<select name='produit_id' required>";
            foreach ($produits as $p) {
                echo "<option value='{$p->id}'>{$p->nom}</option>";
            }
            echo "</select>";
            echo "<button type='submit' style='margin-left: 10px; padding: 5px;'>Réassigner</button>";
            echo "</form>";
        }
        else if ($_GET['action'] === 'do_reassign' && isset($_GET['produit_id'])) {
            $nouveauProduitId = (int)$_GET['produit_id'];
            $nouveauProduit = Produit::find($nouveauProduitId);
            
            if ($nouveauProduit) {
                $mouvement->produit_id = $nouveauProduitId;
                $mouvement->save();
                
                echo "<div style='background-color: #ddffdd; padding: 10px; border: 1px solid green;'>";
                echo "✅ Le mouvement de stock #{$mouvementId} a été réassigné au produit '{$nouveauProduit->nom}'.";
                echo "</div>";
                echo "<p><a href='/mouvements-stock/{$mouvementId}'>Voir le mouvement mis à jour</a></p>";
            } else {
                echo "<div style='background-color: #ffdddd; padding: 10px; border: 1px solid red;'>";
                echo "⚠️ Le produit sélectionné n'existe pas.";
                echo "</div>";
            }
        }
    }
}

// Lien pour retourner à la liste des mouvements
echo "<p><a href='/mouvements-stock'>Retour à la liste des mouvements</a></p>"; 