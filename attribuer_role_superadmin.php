<?php
/**
 * Script pour attribuer le rôle de super-admin à un utilisateur
 * 
 * Utilisation: php attribuer_role_superadmin.php "email@utilisateur.com"
 */

// Bootstrap l'application Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "👑 ATTRIBUTION DU RÔLE SUPER-ADMIN\n";
echo "========================================\n\n";

// Récupérer l'email depuis les arguments ou demander
$email = $argv[1] ?? null;

if (!$email) {
    echo "Email de l'utilisateur: ";
    $email = trim(fgets(STDIN));
}

// Vérifier si l'utilisateur existe
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ Utilisateur avec l'email '{$email}' non trouvé.\n";
    exit(1);
}

echo "✅ Utilisateur trouvé: {$user->name} (ID: {$user->id})\n\n";

// Vérifier si le rôle super-admin existe
if (!class_exists('\Spatie\Permission\Models\Role')) {
    echo "❌ Le package spatie/laravel-permission n'est pas installé.\n";
    exit(1);
}

$superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();

if (!$superAdminRole) {
    echo "⚙️ Le rôle 'super-admin' n'existe pas. Création du rôle...\n";
    
    try {
        $superAdminRole = \Spatie\Permission\Models\Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        echo "✅ Rôle 'super-admin' créé avec succès.\n\n";
    } catch (\Exception $e) {
        echo "❌ Erreur lors de la création du rôle: {$e->getMessage()}\n";
        exit(1);
    }
}

// Vérifier si l'utilisateur a déjà le rôle
if ($user->hasRole('super-admin')) {
    echo "ℹ️ L'utilisateur a déjà le rôle 'super-admin'.\n";
} else {
    try {
        // Supprimer tous les autres rôles (optionnel)
        echo "Voulez-vous supprimer tous les autres rôles de cet utilisateur? (o/n): ";
        $removeOtherRoles = strtolower(trim(fgets(STDIN))) === 'o';
        
        if ($removeOtherRoles) {
            $user->syncRoles(['super-admin']);
            echo "✅ Tous les autres rôles ont été supprimés.\n";
        } else {
            $user->assignRole('super-admin');
        }
        
        echo "✅ Rôle 'super-admin' attribué à {$user->name} avec succès!\n";
    } catch (\Exception $e) {
        echo "❌ Erreur lors de l'attribution du rôle: {$e->getMessage()}\n";
        exit(1);
    }
}

// Afficher les permissions disponibles
try {
    $permissions = \Spatie\Permission\Models\Permission::all();
    
    if ($permissions->count() > 0) {
        echo "\nℹ️ Permissions disponibles:\n";
        foreach ($permissions as $permission) {
            echo "  - {$permission->name}\n";
        }
        
        echo "\nVoulez-vous attribuer toutes les permissions à ce super-admin? (o/n): ";
        $giveAllPermissions = strtolower(trim(fgets(STDIN))) === 'o';
        
        if ($giveAllPermissions) {
            $user->syncPermissions($permissions->pluck('name')->toArray());
            echo "✅ Toutes les permissions ont été attribuées à {$user->name}.\n";
        }
    } else {
        echo "\nℹ️ Aucune permission n'est définie dans le système.\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur lors de la récupération des permissions: {$e->getMessage()}\n";
}

echo "\n========================================\n";
echo "🎉 Opération terminée avec succès!\n";
echo "========================================\n";
echo "\nL'utilisateur {$user->name} ({$email}) est maintenant super-admin.\n";
echo "Il peut accéder à toutes les superettes et fonctionnalités d'administration.\n"; 