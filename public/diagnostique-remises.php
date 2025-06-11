<?php
// Activez l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Informations de base sur l'environnement
echo "<h1>Diagnostic de l'application</h1>";
echo "<p>Version PHP: " . phpversion() . "</p>";
echo "<p>Date et heure du serveur: " . date('Y-m-d H:i:s') . "</p>";

// Vérifiez les extensions PHP nécessaires
echo "<h2>Extensions PHP</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color:green'>✓ {$ext} est chargée</p>";
    } else {
        echo "<p style='color:red'>✗ {$ext} n'est PAS chargée</p>";
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    echo "<p style='color:red; font-weight:bold;'>Certaines extensions requises sont manquantes. Cela peut causer des problèmes avec Laravel.</p>";
}

// Vérification des fichiers clés
echo "<h2>Vérification des fichiers critiques</h2>";
$critical_files = [
    'index.php',
    '../.env',
    '../vendor/autoload.php',
    '../bootstrap/app.php',
    '../app/Http/Controllers/RemiseController.php',
    '../resources/views/remises/index.blade.php'
];

foreach ($critical_files as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "<p style='color:green'>✓ {$file} existe</p>";
    } else {
        echo "<p style='color:red'>✗ {$file} n'existe PAS</p>";
    }
}

// Permissions de dossiers
echo "<h2>Vérification des permissions de dossiers</h2>";
$writable_dirs = [
    '../storage',
    '../storage/logs',
    '../storage/framework',
    '../bootstrap/cache'
];

foreach ($writable_dirs as $dir) {
    $full_path = __DIR__ . '/' . $dir;
    if (is_dir($full_path)) {
        if (is_writable($full_path)) {
            echo "<p style='color:green'>✓ {$dir} existe et est accessible en écriture</p>";
        } else {
            echo "<p style='color:red'>✗ {$dir} existe mais n'est PAS accessible en écriture</p>";
        }
    } else {
        echo "<p style='color:red'>✗ {$dir} n'existe PAS</p>";
    }
}

// Instructions pour la suite
echo "<h2>Étapes suivantes</h2>";
echo "<p>Si vous voyez cette page, votre serveur PHP fonctionne mais il y a peut-être un problème avec Laravel.</p>";
echo "<p>Suggestions :</p>";
echo "<ul>";
echo "<li>Vérifiez le fichier de log Laravel : <code>storage/logs/laravel.log</code></li>";
echo "<li>Essayez de vider le cache avec les commandes artisan : <code>php artisan config:clear</code>, <code>php artisan cache:clear</code>, <code>php artisan view:clear</code></li>";
echo "<li>Redémarrez votre serveur web</li>";
echo "</ul>";

// Lien pour retourner à l'application
echo "<p><a href='/'>Retour à l'application</a></p>";
