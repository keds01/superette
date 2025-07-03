<?php
/**
 * Script de réparation des relations de mouvements de stock
 * 
 * Ce script va analyser tous les mouvements de stock et vérifier
 * que les relations avec les produits (même supprimés) fonctionnent,
 * puis tenter de les réparer si nécessaire.
 */

// Chargement de l'application Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// On utilise directement les modèles
use App\Models\MouvementStock;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Protection basique - nécessite d'être authentifié avec un rôle spécifique
if (!Auth::check() || !Auth::user()->hasRole(['admin', 'manager'])) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 20px;'>";
    echo "⛔️ Accès interdit - Vous devez être connecté avec les droits d'administrateur ou manager.";
    echo "</div>";
    exit;
}

// En-tête HTML
echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Réparation des mouvements de stock</title>";
echo "<style>
    body { font-family: sans-serif; line-height: 1.6; margin: 20px; }
    h1, h2 { color: #333; }
    .success { background-color: #e6ffed; color: #22863a; padding: 10px; border: 1px solid #22863a; border-radius: 4px; margin-bottom: 10px; }
    .warning { background-color: #fff5e6; color: #b08800; padding: 10px; border: 1px solid #b08800; border-radius: 4px; margin-bottom: 10px; }
    .error { background-color: #ffeef0; color: #cb2431; padding: 10px; border: 1px solid #cb2431; border-radius: 4px; margin-bottom: 10px; }
    .info { background-color: #f1f8ff; color: #0366d6; padding: 10px; border: 1px solid #0366d6; border-radius: 4px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:nth-child(even) { background-color: #f9f9f9; }
</style>";
echo "</head>";
echo "<body>";
echo "<h1>Réparation des mouvements de stock</h1>";

// Mode exécution ou analyse simple
$dryRun = !isset($_GET['fix']) || $_GET['fix'] !== '1';
if ($dryRun) {
    echo "<div class='info'>Mode analyse uniquement - aucune modification ne sera apportée à la base de données.</div>";
    echo "<p><a href='?fix=1' style='padding: 8px 16px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Exécuter les réparations</a></p>";
} else {
    echo "<div class='warning'>Mode réparation - les problèmes détectés seront corrigés.</div>";
}

// Récupérer tous les mouvements de stock
echo "<h2>Analyse des mouvements de stock</h2>";
$mouvements = MouvementStock::all();
echo "<p>Total des mouvements : " . $mouvements->count() . "</p>";

// Préparation du tableau de résultats
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Produit ID</th>";
echo "<th>Type</th>";
echo "<th>Date</th>";
echo "<th>Problème</th>";
echo "<th>Action</th>";
echo "<th>Résultat</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

$problèmesCorrigés = 0;
$problèmesDétectés = 0;

foreach ($mouvements as $mouvement) {
    // Vérifier si le produit existe
    $produitExiste = Produit::withTrashed()->where('id', $mouvement->produit_id)->exists();
    
    // Vérifier si la relation fonctionne correctement
    $produitViaRelation = $mouvement->produit;
    $problème = null;
    $action = null;
    $résultat = null;
    
    if (!$produitExiste) {
        $problème = "Le produit #{$mouvement->produit_id} n'existe pas, même parmi les supprimés";
        $action = "Aucune correction possible";
        $résultat = "Non corrigeable";
        $problèmesDétectés++;
    } 
    else if (is_null($produitViaRelation)) {
        $problème = "Le produit existe mais la relation ne fonctionne pas";
        $action = "Réinitialiser la relation";
        
        if (!$dryRun) {
            try {
                // Réparer en réinitialisant la relation du produit
                DB::table('mouvements_stock')
                    ->where('id', $mouvement->id)
                    ->update(['produit_id' => $mouvement->produit_id]);
                    
                $résultat = "Corrigé";
                $problèmesCorrigés++;
            } catch (\Exception $e) {
                $résultat = "Échec : " . $e->getMessage();
            }
        } else {
            $résultat = "Sera corrigé";
        }
        $problèmesDétectés++;
    }
    
    if ($problème) {
        echo "<tr>";
        echo "<td>{$mouvement->id}</td>";
        echo "<td>{$mouvement->produit_id}</td>";
        echo "<td>{$mouvement->type}</td>";
        echo "<td>" . ($mouvement->date_mouvement ?? $mouvement->created_at ?? 'N/A') . "</td>";
        echo "<td>{$problème}</td>";
        echo "<td>{$action}</td>";
        echo "<td>{$résultat}</td>";
        echo "</tr>";
    }
}

echo "</tbody>";
echo "</table>";

// Résumé
echo "<h2>Résumé</h2>";
echo "<ul>";
echo "<li>Total des mouvements : " . $mouvements->count() . "</li>";
echo "<li>Problèmes détectés : {$problèmesDétectés}</li>";

if (!$dryRun) {
    echo "<li>Problèmes corrigés : {$problèmesCorrigés}</li>";
}

echo "</ul>";

if ($dryRun && $problèmesDétectés > 0) {
    echo "<p><a href='?fix=1' style='padding: 8px 16px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Exécuter les réparations</a></p>";
}

echo "<p><a href='/mouvements-stock'>Retour à la liste des mouvements</a></p>";

echo "</body>";