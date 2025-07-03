<?php
/**
 * Script de diagnostic des données du mouvement de stock #26
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
use App\Models\Unite;
use Illuminate\Support\Facades\DB;

echo "<h1>Diagnostic des données du mouvement de stock #26</h1>";

// Récupération du mouvement problématique
$mouvementId = 26;
$mouvement = MouvementStock::find($mouvementId);

if (!$mouvement) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ Le mouvement de stock #{$mouvementId} n'existe pas.";
    echo "</div>";
    exit;
}

// 1. Vérification des données brutes du mouvement
echo "<h2>1. Données brutes du mouvement</h2>";
echo "<pre>";
print_r($mouvement->toArray());
echo "</pre>";

// 2. Vérification du produit associé
echo "<h2>2. Produit associé</h2>";
$produit = Produit::withTrashed()->find($mouvement->produit_id);

if (!$produit) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ Le produit #{$mouvement->produit_id} n'existe pas, même parmi les produits supprimés.";
    echo "</div>";
} else {
    echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
    echo "✅ Le produit existe dans la base de données: {$produit->nom} (ID: {$produit->id})";
    echo "</div>";
    
    echo "<h3>Données du produit</h3>";
    echo "<pre>";
    print_r($produit->toArray());
    echo "</pre>";
}

// 3. Vérification de la relation
echo "<h2>3. Test de la relation produit</h2>";
$mouvementFrais = MouvementStock::find($mouvementId);
$mouvementFrais->load(['produit' => function($query) {
    $query->withTrashed();
}]);

if ($mouvementFrais->produit) {
    echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
    echo "✅ La relation produit se charge correctement.";
    echo "</div>";
} else {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
    echo "⚠️ La relation produit ne se charge pas correctement même avec withTrashed()";
    echo "</div>";
}

// 4. Vérification de l'unité de vente
echo "<h2>4. Unité de vente du produit</h2>";
if ($produit && $produit->unite_vente_id) {
    $unite = Unite::find($produit->unite_vente_id);
    if ($unite) {
        echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
        echo "✅ L'unité de vente existe: {$unite->nom} (Symbole: {$unite->symbole})";
        echo "</div>";
    } else {
        echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
        echo "⚠️ L'unité de vente #{$produit->unite_vente_id} n'existe pas.";
        echo "</div>";
    }
} else {
    echo "<div style='color: orange; padding: 10px; border: 1px solid orange;'>";
    echo "⚠️ Le produit n'a pas d'unité de vente définie.";
    echo "</div>";
}

// 5. Simulation de la vue
echo "<h2>5. Simulation de l'affichage de la vue</h2>";

echo "<h3>Produit</h3>";
if ($mouvementFrais->produit) {
    echo "Nom: " . $mouvementFrais->produit->nom . "<br>";
    echo "Catégorie: " . ($mouvementFrais->produit->categorie ? $mouvementFrais->produit->categorie->nom : 'Non définie') . "<br>";
} else {
    echo "Produit: Non trouvé<br>";
}

echo "<h3>Type de mouvement</h3>";
echo "Type: " . $mouvementFrais->type . "<br>";

echo "<h3>Quantité du mouvement</h3>";
$quantiteMouvement = $mouvementFrais->quantite_apres_conditionnement - $mouvementFrais->quantite_avant_conditionnement;
$produitActif = $mouvementFrais->produit;
echo "Quantité: " . number_format(abs($quantiteMouvement), 2) . " ";
if ($produitActif && $produitActif->uniteVente) {
    echo $produitActif->uniteVente->symbole;
} else {
    echo "(unité non définie)";
}
echo "<br>";
echo "Direction: " . ($quantiteMouvement >= 0 ? '+' : '-') . "<br>";
echo "Avant: " . number_format($mouvementFrais->quantite_avant_conditionnement, 2) . " | Après: " . number_format($mouvementFrais->quantite_apres_conditionnement, 2) . "<br>";

echo "<h3>Date du mouvement</h3>";
echo "Date: " . ($mouvementFrais->date_mouvement ? $mouvementFrais->date_mouvement->format('d/m/Y H:i') : ($mouvementFrais->created_at ? $mouvementFrais->created_at->format('d/m/Y H:i') : 'Date non définie')) . "<br>";

echo "<h3>Motif</h3>";
echo "Motif: " . ($mouvementFrais->motif ?: 'Aucun motif spécifié') . "<br>";

// 6. Vérification des relations chargées dans le contrôleur
echo "<h2>6. Test du chargement comme dans le contrôleur</h2>";

$stockMovement = MouvementStock::find($mouvementId);
echo "Produit ID initial: " . $stockMovement->produit_id . "<br>";

$produitManuellement = Produit::withTrashed()->find($stockMovement->produit_id);
echo "Produit trouvé manuellement: " . ($produitManuellement ? 'OUI' : 'NON') . "<br>";

// Si le produit est trouvé manuellement mais pas via la relation, on l'attribue directement
if ($produitManuellement) {
    $stockMovement->produit = $produitManuellement;
    echo "Produit assigné manuellement.<br>";
} else {
    // On doit recharger la relation car un accès précédent pourrait l'avoir mise en cache à null
    $stockMovement->load('produit');
    $produitViaRelation = $stockMovement->produit;
    echo "Produit via relation est null: " . (is_null($produitViaRelation) ? 'OUI' : 'NON') . "<br>";

    if (is_null($produitViaRelation)) {
        echo "!!! Incohérence: Le produit ID " . $stockMovement->produit_id . " n'a pas été trouvé.<br>";
    }
}

// On charge les autres relations nécessaires
$stockMovement->load(['produit.categorie', 'produit.uniteVente', 'utilisateur']);

echo "<h3>Résultat final après chargement des relations</h3>";
echo "Produit chargé: " . ($stockMovement->produit ? 'OUI' : 'NON') . "<br>";
if ($stockMovement->produit) {
    echo "Nom du produit: " . $stockMovement->produit->nom . "<br>";
    echo "Catégorie: " . ($stockMovement->produit->categorie ? $stockMovement->produit->categorie->nom : 'Non définie') . "<br>";
    echo "Unité de vente: " . ($stockMovement->produit->uniteVente ? $stockMovement->produit->uniteVente->symbole : 'Non définie') . "<br>";
}

// 7. Réparation de la relation si nécessaire
echo "<h2>7. Réparation de la relation</h2>";

if (!$stockMovement->produit && $produitManuellement) {
    echo "<div style='color: orange; padding: 10px; border: 1px solid orange;'>";
    echo "⚠️ La relation est cassée mais le produit existe. Vous pouvez réparer cette relation.";
    echo "</div>";
    
    if (isset($_GET['action']) && $_GET['action'] === 'repair') {
        try {
            // Utilisation directe de DB pour forcer la mise à jour
            DB::statement("UPDATE mouvements_stock SET produit_id = ? WHERE id = ?", [$produitManuellement->id, $mouvementId]);
            
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
} else {
    echo "<div style='color: green; padding: 10px; border: 1px solid green;'>";
    echo "✅ La relation semble fonctionner correctement, aucune réparation nécessaire.";
    echo "</div>";
}

// Lien pour retourner à la page du mouvement
echo "<p><a href='/mouvements-stock/{$mouvementId}'>Retourner à la page du mouvement</a></p>";
echo "<p><a href='/mouvements-stock'>Retour à la liste des mouvements</a></p>"; 