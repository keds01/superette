<?php

/**
 * Script de mise à jour des contrôleurs pour Laravel 12
 * Ce script supprime les appels à middleware() dans les constructeurs des contrôleurs
 * qui ne sont plus supportés dans Laravel 12
 */

// Répertoire des contrôleurs
$controllersDir = __DIR__ . '/app/Http/Controllers';

// Fonction pour traiter un fichier de contrôleur
function processControllerFile($filePath) {
    echo "Traitement du fichier: " . basename($filePath) . PHP_EOL;
    
    $content = file_get_contents($filePath);
    
    // Vérifier si le fichier contient des appels à middleware()
    if (strpos($content, 'middleware(') === false) {
        echo "  - Aucun appel à middleware() trouvé" . PHP_EOL;
        return;
    }
    
    // Modèle pour capturer tout le constructeur
    $pattern = '/(public\s+function\s+__construct\s*\([^)]*\)\s*\{)(.*?)(\})/s';
    
    // Fonction de remplacement
    $replacement = function($matches) {
        $constructorStart = $matches[1];
        $constructorBody = $matches[2];
        $constructorEnd = $matches[3];
        
        // Enlever toutes les lignes contenant middleware()
        $newBody = preg_replace('/\s*\$this->middleware\([^;]*\);/m', '', $constructorBody);
        
        // Ajouter un commentaire explicatif
        $newBody = $newBody . "\n        // Note: Les middlewares ont été déplacés dans routes/web.php pour compatibilité avec Laravel 12\n";
        
        return $constructorStart . $newBody . $constructorEnd;
    };
    
    // Appliquer le remplacement
    $newContent = preg_replace_callback($pattern, $replacement, $content);
    
    // Vérifier si des modifications ont été effectuées
    if ($newContent !== $content) {
        file_put_contents($filePath, $newContent);
        echo "  - Appels à middleware() supprimés avec succès" . PHP_EOL;
    } else {
        echo "  - Aucune modification nécessaire (structure différente)" . PHP_EOL;
    }
}

// Parcourir tous les fichiers de contrôleurs
function processDirectory($dir) {
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            processDirectory($path);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            processControllerFile($path);
        }
    }
}

echo "=== Début de la mise à jour des contrôleurs pour Laravel 12 ===" . PHP_EOL;
processDirectory($controllersDir);
echo "=== Mise à jour terminée ===" . PHP_EOL;
