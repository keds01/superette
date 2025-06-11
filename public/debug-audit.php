<?php
require '../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Instancier le service d'audit
$auditService = app(\App\Services\AuditService::class);

echo "<h1>Diagnostic du service d'audit</h1>";

// Vérifier les modèles et tables
echo "<h2>1. Vérification des modèles et tables</h2>";
echo "<ul>";
try {
    echo "<li>ActivityLog: " . (\App\Models\ActivityLog::count() ?? 'N/A') . " enregistrements</li>";
} catch (\Exception $e) {
    echo "<li style='color:red'>ActivityLog: ERREUR - " . $e->getMessage() . "</li>";
}

try {
    echo "<li>Produit: " . (\App\Models\Produit::count() ?? 'N/A') . " enregistrements</li>";
} catch (\Exception $e) {
    echo "<li style='color:red'>Produit: ERREUR - " . $e->getMessage() . "</li>";
}

try {
    echo "<li>MouvementStock: " . (\App\Models\MouvementStock::count() ?? 'N/A') . " enregistrements</li>";
} catch (\Exception $e) {
    echo "<li style='color:red'>MouvementStock: ERREUR - " . $e->getMessage() . "</li>";
}

try {
    echo "<li>Vente: " . (\App\Models\Vente::count() ?? 'N/A') . " enregistrements</li>";
} catch (\Exception $e) {
    echo "<li style='color:red'>Vente: ERREUR - " . $e->getMessage() . "</li>";
}

try {
    echo "<li>User: " . (\App\Models\User::count() ?? 'N/A') . " enregistrements</li>";
} catch (\Exception $e) {
    echo "<li style='color:red'>User: ERREUR - " . $e->getMessage() . "</li>";
}

try {
    echo "<li>Paiement: " . (\App\Models\Paiement::count() ?? 'N/A') . " enregistrements</li>";
} catch (\Exception $e) {
    echo "<li style='color:red'>Paiement: ERREUR - " . $e->getMessage() . "</li>";
}
echo "</ul>";

// Test des variables du contrôleur
echo "<h2>2. Test des variables du contrôleur AuditController@index</h2>";

// Total activités
try {
    $totalActivites = \App\Models\ActivityLog::count();
    echo "<p>totalActivites = {$totalActivites}</p>";
} catch (\Exception $e) {
    echo "<p style='color:red'>totalActivites: ERREUR - " . $e->getMessage() . "</p>";
}

// Dernières activités
try {
    $dernieresActivites = \App\Models\ActivityLog::with('user')->latest()->take(10)->get();
    echo "<p>dernieresActivites: " . $dernieresActivites->count() . " éléments récupérés</p>";
    if ($dernieresActivites->isNotEmpty()) {
        echo "<ul>";
        foreach ($dernieresActivites as $index => $activite) {
            if ($index >= 3) break; // Limiter à 3 pour la lisibilité
            echo "<li>ID: {$activite->id}, Type: {$activite->type}, User: " . 
                 ($activite->user ? $activite->user->name : "NULL") . "</li>";
        }
        echo "</ul>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>dernieresActivites: ERREUR - " . $e->getMessage() . "</p>";
}

// Tester detecterAnomalies
echo "<h2>3. Test detecterAnomalies()</h2>";
try {
    $anomalies = $auditService->detecterAnomalies();
    echo "<p>Nombre d'anomalies détectées: " . count($anomalies) . "</p>";
    if (count($anomalies) > 0) {
        echo "<details><summary>Première anomalie</summary>";
        echo "<pre>" . print_r($anomalies[0], true) . "</pre>";
        echo "</details>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>ERREUR dans detecterAnomalies(): " . $e->getMessage() . "</p>";
    echo "<details><summary>Stack trace</summary>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</details>";
}

// Tester séparément chaque méthode de détection d'anomalies
echo "<h3>3.1. Test de chaque détecteur d'anomalie</h3>";

// detecterVariationsPrix
echo "<h4>3.1.1. detecterVariationsPrix</h4>";
try {
    $anomalies = [];
    $reflection = new ReflectionMethod($auditService, 'detecterVariationsPrix');
    $reflection->setAccessible(true);
    $reflection->invoke($auditService, &$anomalies);
    echo "<p>Anomalies de variation de prix: " . count($anomalies) . "</p>";
} catch (\Exception $e) {
    echo "<p style='color:red'>ERREUR: " . $e->getMessage() . "</p>";
}

// detecterAnnulationsSuspectes
echo "<h4>3.1.2. detecterAnnulationsSuspectes</h4>";
try {
    $anomalies = [];
    $reflection = new ReflectionMethod($auditService, 'detecterAnnulationsSuspectes');
    $reflection->setAccessible(true);
    $reflection->invoke($auditService, &$anomalies);
    echo "<p>Anomalies d'annulations suspectes: " . count($anomalies) . "</p>";
} catch (\Exception $e) {
    echo "<p style='color:red'>ERREUR: " . $e->getMessage() . "</p>";
}

// detecterAjustementsStockMassifs
echo "<h4>3.1.3. detecterAjustementsStockMassifs</h4>";
try {
    $anomalies = [];
    $reflection = new ReflectionMethod($auditService, 'detecterAjustementsStockMassifs');
    $reflection->setAccessible(true);
    $reflection->invoke($auditService, &$anomalies);
    echo "<p>Anomalies d'ajustements de stock massifs: " . count($anomalies) . "</p>";
} catch (\Exception $e) {
    echo "<p style='color:red'>ERREUR: " . $e->getMessage() . "</p>";
}

// Tester genererRapportQuotidien
echo "<h2>4. Test genererRapportQuotidien()</h2>";
try {
    $rapport = $auditService->genererRapportQuotidien();
    echo "<p>Rapport généré: " . (is_array($rapport) ? "Oui" : "Non") . "</p>";
    
    echo "<h3>Structure du rapport:</h3>";
    echo "<ul>";
    
    if (isset($rapport['date'])) echo "<li>date: " . $rapport['date'] . "</li>";
    else echo "<li style='color:orange'>date: MANQUANT</li>";
    
    if (isset($rapport['ventes'])) {
        echo "<li>ventes: ";
        if (is_array($rapport['ventes'])) {
            echo "<ul>";
            if (isset($rapport['ventes']['total'])) echo "<li>total: " . $rapport['ventes']['total'] . "</li>";
            if (isset($rapport['ventes']['montant_total'])) echo "<li>montant_total: " . $rapport['ventes']['montant_total'] . "</li>";
            if (isset($rapport['ventes']['panier_moyen'])) echo "<li>panier_moyen: " . $rapport['ventes']['panier_moyen'] . "</li>";
            echo "</ul>";
        } else {
            echo "Format invalide";
        }
        echo "</li>";
    } else {
        echo "<li style='color:orange'>ventes: MANQUANT</li>";
    }
    
    if (isset($rapport['paiements'])) {
        echo "<li>paiements: Présent</li>";
    } else {
        echo "<li style='color:orange'>paiements: MANQUANT</li>";
    }
    
    if (isset($rapport['activites'])) {
        echo "<li>activites: Présent</li>";
    } else {
        echo "<li style='color:orange'>activites: MANQUANT</li>";
    }
    echo "</ul>";
} catch (\Exception $e) {
    echo "<p style='color:red'>ERREUR dans genererRapportQuotidien(): " . $e->getMessage() . "</p>";
    echo "<details><summary>Stack trace</summary>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</details>";
}

// Vérification de la relation dans ActivityLog
echo "<h2>5. Vérification de la relation user dans ActivityLog</h2>";
try {
    $relationTest = \App\Models\ActivityLog::with('user')->first();
    if ($relationTest) {
        echo "<p>Relation user: " . ($relationTest->user ? "OK - ID: " . $relationTest->user->id . ", Nom: " . $relationTest->user->name : "NULL - La relation existe mais aucun utilisateur associé") . "</p>";
    } else {
        echo "<p style='color:orange'>Impossible de tester la relation: aucun enregistrement ActivityLog trouvé</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>ERREUR dans la relation: " . $e->getMessage() . "</p>";
}

// Vérifier la structure du modèle ActivityLog
echo "<h2>6. Structure du modèle ActivityLog</h2>";
try {
    $schema = \Illuminate\Support\Facades\Schema::getColumnListing('activity_logs');
    
    if (!empty($schema)) {
        echo "<p>Colonnes de la table activity_logs:</p>";
        echo "<ul>";
        foreach ($schema as $column) {
            echo "<li>{$column}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>La table activity_logs n'existe pas ou est vide</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>ERREUR lors de la récupération du schéma: " . $e->getMessage() . "</p>";
}
