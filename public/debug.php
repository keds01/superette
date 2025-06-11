<?php
// Activer l'affichage de toutes les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Informations sur l'environnement
echo "Version PHP: " . phpversion() . "<br>";
echo "Laravel: " . app()->version() . "<br>";
echo "Debug activé: " . (config('app.debug') ? 'Oui' : 'Non') . "<br>";

// Tester l'accès à la base de données
try {
    $result = DB::select('SELECT 1');
    echo "Connexion à la base de données: OK<br>";
} catch (Exception $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage() . "<br>";
}

// Tester l'accès au contrôleur des remises
try {
    $controller = app()->make('App\Http\Controllers\RemiseController');
    echo "Contrôleur RemiseController: OK<br>";
} catch (Exception $e) {
    echo "Erreur avec RemiseController: " . $e->getMessage() . "<br>";
}

// Afficher les routes disponibles pour remises
echo "<h3>Routes pour les remises:</h3>";
$routes = Route::getRoutes();
foreach ($routes as $route) {
    if (strpos($route->uri, 'remises') !== false) {
        echo $route->methods[0] . " " . $route->uri . " => " . $route->getActionName() . "<br>";
    }
}
