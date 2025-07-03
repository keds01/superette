<?php
/**
 * Script de réparation de tous les mouvements de stock problématiques
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

echo "<h1>Réparation de tous les mouvements de stock</h1>";

// Récupération de tous les mouvements
$mouvements = MouvementStock::all();
$total = $mouvements->count();

echo "<p>Nombre total de mouvements : {$total}</p>";

// Compteurs
$problemes = 0;
$repares = 0;
$nonReparables = 0;

// Mode exécution
$modeReparation = isset($_GET['action']) && $_GET['action'] === 'repair';

if (!$modeReparation) {
    echo "<div style='background-color: #f0f8ff; padding: 10px; border: 1px solid blue; margin-bottom: 20px;'>";
    echo "Mode analyse uniquement. Aucune modification ne sera effectuée. ";
    echo "<a href='?action=repair' style='font-weight: bold;'>Cliquez ici pour réparer</a>";
    echo "</div>";
}

echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #f2f2f2;'>";
echo "<th>ID</th>";
echo "<th>Produit ID</th>";
echo "<th>Type</th>";
echo "<th>Problème</th>";
echo "<th>Action</th>";
echo "<th>Résultat</th>";
echo "</tr>";

foreach ($mouvements as $mouvement) {
    // Vérifier si la relation fonctionne
    $mouvement->load('produit');
    $produitViaRelation = $mouvement->produit;
    
    // Vérifier si le produit existe manuellement
    $produitManuellement = Produit::withTrashed()->find($mouvement->produit_id);
    
    $probleme = null;
    $action = null;
    $resultat = null;
    
    if (!$produitManuellement) {
        // Le produit n'existe pas du tout
        $probleme = "Le produit #{$mouvement->produit_id} n'existe pas, même parmi les supprimés";
        $action = "Aucune action possible";
        $resultat = "Non réparable";
        $nonReparables++;
        $problemes++;
    } 
    else if (!$produitViaRelation) {
        // Le produit existe mais la relation ne fonctionne pas
        $probleme = "Relation cassée avec le produit #{$mouvement->produit_id}";
        $action = "Réassigner le produit";
        
        if ($modeReparation) {
            try {
                // Forcer la mise à jour de la relation
                DB::statement("UPDATE mouvements_stock SET produit_id = ? WHERE id = ?", [$produitManuellement->id, $mouvement->id]);
                
                // Vérifier si la réparation a fonctionné
                $mouvementVerif = MouvementStock::find($mouvement->id);
                $mouvementVerif->load('produit');
                
                if ($mouvementVerif->produit) {
                    $resultat = "Réparé";
                    $repares++;
                } else {
                    $resultat = "Échec de la réparation";
                    $nonReparables++;
                }
            } catch (\Exception $e) {
                $resultat = "Erreur: " . $e->getMessage();
                $nonReparables++;
            }
        } else {
            $resultat = "À réparer";
        }
        
        $problemes++;
    }
    
    // Afficher les mouvements problématiques dans le tableau
    if ($probleme) {
        echo "<tr>";
        echo "<td>{$mouvement->id}</td>";
        echo "<td>{$mouvement->produit_id}</td>";
        echo "<td>{$mouvement->type}</td>";
        echo "<td>{$probleme}</td>";
        echo "<td>{$action}</td>";
        echo "<td>{$resultat}</td>";
        echo "</tr>";
    }
}

echo "</table>";

// Résumé
echo "<h2>Résumé</h2>";
echo "<ul>";
echo "<li>Mouvements analysés : {$total}</li>";
echo "<li>Problèmes détectés : {$problemes}</li>";

if ($modeReparation) {
    echo "<li>Mouvements réparés : {$repares}</li>";
    echo "<li>Mouvements non réparables : {$nonReparables}</li>";
} else if ($problemes > 0) {
    echo "<li><a href='?action=repair' style='font-weight: bold;'>Cliquez ici pour réparer tous les problèmes</a></li>";
}

echo "</ul>";

echo "<p><a href='/mouvements-stock'>Retour à la liste des mouvements</a></p>"; 