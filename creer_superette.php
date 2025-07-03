<?php
/**
 * Script pour créer rapidement une nouvelle superette
 * 
 * Utilisation: php creer_superette.php "Nom Superette" "CODE_UNIQUE" "Adresse" "Téléphone" "email@superette.com"
 */

// Bootstrap l'application Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Superette;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "========================================\n";
echo "🏪 CRÉATION D'UNE NOUVELLE SUPERETTE\n";
echo "========================================\n\n";

// Récupérer les arguments ou demander les informations
$nom = $argv[1] ?? null;
$code = $argv[2] ?? null;
$adresse = $argv[3] ?? null;
$telephone = $argv[4] ?? null;
$email = $argv[5] ?? null;

if (!$nom) {
    echo "Nom de la superette: ";
    $nom = trim(fgets(STDIN));
}

if (!$code) {
    echo "Code unique (ex: SP002): ";
    $code = trim(fgets(STDIN));
    
    // Vérifier si le code existe déjà
    while (Superette::where('code', $code)->exists()) {
        echo "❌ Ce code existe déjà. Veuillez en choisir un autre: ";
        $code = trim(fgets(STDIN));
    }
}

if (!$adresse) {
    echo "Adresse: ";
    $adresse = trim(fgets(STDIN));
}

if (!$telephone) {
    echo "Téléphone: ";
    $telephone = trim(fgets(STDIN));
}

if (!$email) {
    echo "Email: ";
    $email = trim(fgets(STDIN));
}

// Demander une description
echo "Description (optionnelle): ";
$description = trim(fgets(STDIN));

// Création de la superette
try {
    $superette = Superette::create([
        'nom' => $nom,
        'code' => $code,
        'adresse' => $adresse,
        'telephone' => $telephone,
        'email' => $email,
        'description' => $description,
        'actif' => true
    ]);
    
    echo "\n✅ Superette '{$nom}' créée avec succès! (ID: {$superette->id})\n\n";
    
    // Proposer de créer un utilisateur pour cette superette
    echo "Voulez-vous créer un utilisateur administrateur pour cette superette? (o/n): ";
    $createUser = strtolower(trim(fgets(STDIN))) === 'o';
    
    if ($createUser) {
        echo "Nom de l'utilisateur: ";
        $userName = trim(fgets(STDIN));
        
        echo "Email de l'utilisateur: ";
        $userEmail = trim(fgets(STDIN));
        
        // Vérifier si l'email existe déjà
        while (User::where('email', $userEmail)->exists()) {
            echo "❌ Cet email existe déjà. Veuillez en choisir un autre: ";
            $userEmail = trim(fgets(STDIN));
        }
        
        echo "Mot de passe (laissez vide pour générer automatiquement): ";
        $userPassword = trim(fgets(STDIN));
        
        if (empty($userPassword)) {
            $userPassword = Str::random(10);
            echo "Mot de passe généré: {$userPassword}\n";
        }
        
        $user = User::create([
            'name' => $userName,
            'email' => $userEmail,
            'password' => Hash::make($userPassword),
            'superette_id' => $superette->id
        ]);
        
        // Attribuer le rôle d'administrateur
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
            if ($adminRole) {
                $user->assignRole($adminRole);
                echo "✅ Rôle 'admin' attribué à l'utilisateur.\n";
            } else {
                echo "⚠️ Rôle 'admin' non trouvé. L'utilisateur a été créé sans rôle.\n";
            }
        }
        
        echo "\n✅ Utilisateur '{$userName}' créé avec succès!\n";
        echo "   Email: {$userEmail}\n";
        echo "   Mot de passe: {$userPassword}\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Erreur lors de la création de la superette: {$e->getMessage()}\n";
}

echo "\n========================================\n";
echo "🎉 Opération terminée!\n";
echo "========================================\n"; 