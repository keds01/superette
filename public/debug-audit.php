<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Vente;
use App\Models\Produit;
use App\Models\MouvementStock;
use Carbon\Carbon;

echo "<h1>Debug Audit - Génération de données</h1>";

try {
    // Vérifier si des utilisateurs existent
    $users = User::all();
    if ($users->isEmpty()) {
        echo "<p style='color: red;'>Aucun utilisateur trouvé. Impossible de créer des activités.</p>";
        exit;
    }

    // Supprimer les anciennes activités d'exemple
    ActivityLog::where('metadata', 'like', '%"exemple":true%')->delete();
    echo "<p>✓ Anciennes activités d'exemple supprimées</p>";

    // Créer des activités variées
    $types = [
        'connexion' => 'Connexion au système',
        'modification' => 'Modification d\'un produit',
        'creation' => 'Création d\'une nouvelle vente',
        'suppression' => 'Suppression d\'un enregistrement',
        'consultation' => 'Consultation du rapport',
        'ajustement_stock' => 'Ajustement du stock',
        'annulation_vente' => 'Annulation d\'une vente',
        'export_data' => 'Export de données'
    ];

    $modelTypes = [
        'App\Models\Produit',
        'App\Models\Vente',
        'App\Models\User',
        'App\Models\Stock',
        'App\Models\Client'
    ];

    $activitesCreees = 0;
    
    for ($i = 0; $i < 25; $i++) {
        $user = $users->random();
        $typeKeys = array_keys($types);
        $typeIndex = array_rand($typeKeys);
        $typeName = $typeKeys[$typeIndex];
        $description = $types[$typeName];
        $modelType = $modelTypes[array_rand($modelTypes)];
        
        // Date aléatoire dans les 7 derniers jours
        $date = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
        
        ActivityLog::create([
            'type' => $typeName,
            'description' => $description,
            'user_id' => $user->id,
            'model_type' => $modelType,
            'model_id' => rand(1, 100),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'metadata' => json_encode([
                'exemple' => true,
                'generated_at' => now()->toISOString(),
                'session_id' => 'session_' . rand(1000, 9999)
            ]),
            'created_at' => $date,
            'updated_at' => $date
        ]);
        
        $activitesCreees++;
    }

    echo "<p>✓ {$activitesCreees} activités créées</p>";

    // Statistiques
    $totalActivites = ActivityLog::count();
    $activitesAujourdHui = ActivityLog::whereDate('created_at', today())->count();
    $activitesCeMois = ActivityLog::whereMonth('created_at', now()->month)->count();

    echo "<h2>Statistiques</h2>";
    echo "<ul>";
    echo "<li>Total activités: {$totalActivites}</li>";
    echo "<li>Activités aujourd'hui: {$activitesAujourdHui}</li>";
    echo "<li>Activités ce mois: {$activitesCeMois}</li>";
    echo "</ul>";

    // Dernières activités
    echo "<h2>Dernières activités</h2>";
    $dernieresActivites = ActivityLog::with('user')->latest()->take(5)->get();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Type</th><th>Description</th><th>Utilisateur</th><th>Date</th></tr>";
    
    foreach ($dernieresActivites as $activite) {
        echo "<tr>";
        echo "<td>{$activite->type}</td>";
        echo "<td>{$activite->description}</td>";
        echo "<td>" . ($activite->user ? $activite->user->name : 'Système') . "</td>";
        echo "<td>{$activite->created_at->format('d/m/Y H:i')}</td>";
        echo "</tr>";
    }
    
    echo "</table>";

    echo "<h2>Test de la page d'audit</h2>";
    echo "<p><a href='/audit' target='_blank'>Cliquer ici pour ouvrir la page d'audit</a></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}
?>

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
