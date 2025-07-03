<?php
/**
 * Script de diagnostic des erreurs
 * Pour déboguer les pages blanches dans l'application
 */

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostic des erreurs Laravel</h1>";

// Charger Laravel pour accéder aux logs et aux classes
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Si on accède avec ?mode=error_log, afficher les dernières erreurs du log
if (isset($_GET['mode']) && $_GET['mode'] === 'error_log') {
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        echo "<h2>Dernières entrées du journal d'erreurs</h2>";
        echo "<pre style='background-color: #f5f5f5; padding: 15px; overflow: auto; max-height: 500px; font-size: 12px;'>";
        
        // Afficher les 100 dernières lignes pour éviter de surcharger la page
        $logs = shell_exec("tail -n 500 " . escapeshellarg($logPath));
        echo htmlspecialchars($logs);
        
        echo "</pre>";
    } else {
        echo "<div style='color: red;'>Fichier de log non trouvé : $logPath</div>";
    }
}

// Si on accède avec ?mode=routes, afficher les routes disponibles
if (isset($_GET['mode']) && $_GET['mode'] === 'routes') {
    echo "<h2>Routes définies dans l'application</h2>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Méthode</th><th>URI</th><th>Nom</th><th>Action</th></tr>";
    
    $router = $app->make('router');
    foreach ($router->getRoutes() as $route) {
        echo "<tr>";
        echo "<td>" . implode('|', $route->methods()) . "</td>";
        echo "<td>" . $route->uri() . "</td>";
        echo "<td>" . $route->getName() . "</td>";
        echo "<td>" . $route->getActionName() . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// Si on accède avec ?mode=models&model=X, vérifier la structure des modèles
if (isset($_GET['mode']) && $_GET['mode'] === 'models' && !empty($_GET['model'])) {
    $modelName = $_GET['model'];
    $className = "\\App\\Models\\$modelName";
    
    echo "<h2>Structure du modèle : $modelName</h2>";
    
    if (class_exists($className)) {
        try {
            $model = new $className();
            
            echo "<h3>Propriétés du modèle</h3>";
            echo "<ul>";
            echo "<li><strong>Table :</strong> " . $model->getTable() . "</li>";
            echo "<li><strong>Clé primaire :</strong> " . $model->getKeyName() . "</li>";
            if (isset($model->fillable)) echo "<li><strong>Champs autorisés :</strong> " . implode(', ', $model->fillable) . "</li>";
            if (isset($model->hidden)) echo "<li><strong>Champs cachés :</strong> " . implode(', ', $model->hidden) . "</li>";
            if (isset($model->casts)) {
                echo "<li><strong>Conversions :</strong> <ul>";
                foreach ($model->casts as $attribute => $cast) {
                    echo "<li>$attribute => $cast</li>";
                }
                echo "</ul></li>";
            }
            echo "</ul>";
            
            echo "<h3>Relations définies</h3>";
            $relationships = [];
            $methods = get_class_methods($model);
            foreach ($methods as $method) {
                if (!in_array($method, ['__construct', '__call', '__callStatic', '__get', '__set'])) {
                    // Vérifier si la méthode peut être appelée sans arguments
                    $reflection = new ReflectionMethod($className, $method);
                    $params = $reflection->getParameters();
                    $requiredParams = 0;
                    foreach ($params as $param) {
                        if (!$param->isOptional()) $requiredParams++;
                    }
                    
                    if ($requiredParams === 0) {
                        try {
                            $return = $model->$method();
                            if (
                                is_object($return) && 
                                (
                                    $return instanceof \Illuminate\Database\Eloquent\Relations\Relation ||
                                    $return instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo ||
                                    $return instanceof \Illuminate\Database\Eloquent\Relations\HasMany ||
                                    $return instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany ||
                                    $return instanceof \Illuminate\Database\Eloquent\Relations\HasOne
                                )
                            ) {
                                $relationships[] = [
                                    'name' => $method,
                                    'type' => class_basename(get_class($return)),
                                    'related' => get_class($return->getRelated())
                                ];
                            }
                        } catch (\Throwable $e) {
                            // Ignorer les erreurs
                        }
                    }
                }
            }
            
            if (count($relationships) > 0) {
                echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
                echo "<tr><th>Nom</th><th>Type</th><th>Modèle lié</th></tr>";
                foreach ($relationships as $rel) {
                    echo "<tr>";
                    echo "<td>{$rel['name']}</td>";
                    echo "<td>{$rel['type']}</td>";
                    echo "<td>{$rel['related']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Aucune relation détectée.</p>";
            }
            
            // Vérifier si la table existe et sa structure
            try {
                $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM " . $model->getTable());
                
                echo "<h3>Structure de la table</h3>";
                echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
                echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
                
                foreach ($columns as $column) {
                    echo "<tr>";
                    echo "<td>" . $column->Field . "</td>";
                    echo "<td>" . $column->Type . "</td>";
                    echo "<td>" . $column->Null . "</td>";
                    echo "<td>" . $column->Key . "</td>";
                    echo "<td>" . $column->Default . "</td>";
                    echo "<td>" . $column->Extra . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } catch (\Exception $e) {
                echo "<div style='color: red;'>Erreur lors de la vérification de la table : " . $e->getMessage() . "</div>";
            }
            
        } catch (\Throwable $e) {
            echo "<div style='color: red;'>Erreur lors de l'instanciation du modèle : " . $e->getMessage() . "</div>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    } else {
        echo "<div style='color: red;'>Le modèle '$className' n'existe pas!</div>";
    }
}

// Si on accède avec ?mode=view&name=X, vérifier la vue et l'afficher
if (isset($_GET['mode']) && $_GET['mode'] === 'view' && !empty($_GET['name'])) {
    $viewName = $_GET['name'];
    
    echo "<h2>Vérification de la vue : $viewName</h2>";
    
    try {
        $viewExists = view()->exists($viewName);
        if ($viewExists) {
            echo "<div style='color: green;'>✓ La vue existe.</div>";
            $viewPath = view($viewName)->getPath();
            echo "<p>Chemin de la vue : $viewPath</p>";
            
            echo "<h3>Contenu de la vue</h3>";
            echo "<pre style='background-color: #f5f5f5; padding: 15px; overflow: auto; max-height: 300px; font-size: 12px;'>";
            echo htmlspecialchars(file_get_contents($viewPath));
            echo "</pre>";
            
            // Tenter d'afficher la vue avec des données minimales
            echo "<h3>Rendu de la vue (tentative)</h3>";
            echo "<div style='border: 1px solid #ccc; padding: 15px;'>";
            try {
                echo view($viewName, [
                    'date_debut' => new \Carbon\Carbon(),
                    'date_fin' => new \Carbon\Carbon(),
                    'type' => 'debug',
                    'ventes' => collect(),
                    'mouvements' => collect(),
                    'stocks' => collect(),
                    'evolutionVentes' => collect(),
                    'categories' => collect(),
                    'chiffre_affaires' => 0,
                    'nombre_ventes' => 0,
                    'valeur_stock' => 0,
                    'marge_brute' => 0,
                ])->render();
            } catch (\Throwable $e) {
                echo "<div style='color: red;'>Erreur lors du rendu de la vue : " . $e->getMessage() . "</div>";
            }
            echo "</div>";
        } else {
            echo "<div style='color: red;'>✗ La vue n'existe pas!</div>";
            
            // Vérifier si des vues similaires existent
            $finder = new \Illuminate\View\FileViewFinder(
                new \Illuminate\Filesystem\Filesystem(), 
                [resource_path('views')]
            );
            
            $allFiles = [];
            foreach ($finder->getPaths() as $path) {
                $directory = new \RecursiveDirectoryIterator($path);
                $iterator = new \RecursiveIteratorIterator($directory);
                $regex = new \RegexIterator($iterator, '/^.+\.blade\.php$/i', \RecursiveRegexIterator::GET_MATCH);
                foreach ($regex as $file) {
                    $name = str_replace([$path . '/', '.blade.php'], ['', ''], $file[0]);
                    $name = str_replace('/', '.', $name);
                    $allFiles[] = $name;
                }
            }
            
            // Rechercher des vues similaires
            $similar = [];
            foreach ($allFiles as $file) {
                if (strpos($file, basename($viewName)) !== false || 
                    strpos(basename($viewName), $file) !== false ||
                    similar_text($file, $viewName) > 5) {
                    $similar[] = $file;
                }
            }
            
            if (count($similar) > 0) {
                echo "<p>Vues similaires trouvées :</p>";
                echo "<ul>";
                foreach ($similar as $view) {
                    echo "<li><a href='?mode=view&name=$view'>$view</a></li>";
                }
                echo "</ul>";
            }
        }
    } catch (\Throwable $e) {
        echo "<div style='color: red;'>Erreur lors de la vérification de la vue : " . $e->getMessage() . "</div>";
    }
}

// Afficher les options disponibles
echo "<h2>Options de diagnostic</h2>";
echo "<ul>";
echo "<li><a href='?mode=error_log'>Afficher les erreurs récentes</a></li>";
echo "<li><a href='?mode=routes'>Lister toutes les routes</a></li>";
echo "<li><a href='?mode=view&name=reports.index'>Vérifier la vue reports.index</a></li>";
echo "<li><a href='?mode=view&name=reports.categories'>Vérifier la vue reports.categories</a></li>";
echo "<li><a href='?mode=models&model=Categorie'>Examiner le modèle Categorie</a></li>";
echo "<li><a href='?mode=models&model=Produit'>Examiner le modèle Produit</a></li>";
echo "</ul>";

// Retourner un bouton de test vers la page des rapports
echo "<div style='margin-top: 30px;'>";
echo "<a href='/reports' style='padding: 10px 20px; background-color: #3490dc; color: white; text-decoration: none; border-radius: 4px;' target='_blank'>Ouvrir la page des rapports</a>";
echo "</div>";