<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VÉRIFICATION DES TABLES DE PERMISSIONS ===\n\n";

// Vérifier si les tables existent
$tables = ['roles', 'permissions', 'role_permission', 'user_role'];
foreach ($tables as $table) {
    $exists = DB::connection()->getSchemaBuilder()->hasTable($table);
    echo "Table '$table': " . ($exists ? "Existe ✓" : "N'existe pas ✗") . "\n";
    
    if ($exists) {
        $count = DB::table($table)->count();
        echo "- Nombre d'enregistrements: $count\n";
        
        if ($count > 0) {
            echo "- Premiers enregistrements:\n";
            $records = DB::table($table)->limit(5)->get();
            foreach ($records as $record) {
                echo "  - " . json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            }
            echo "\n";
        }
    }
    echo "\n";
}

// Vérifier si nous avons un utilisateur admin avec son rôle
echo "=== UTILISATEURS ET LEURS RÔLES ===\n\n";
$users = DB::table('users')->get();
foreach ($users as $user) {
    echo "Utilisateur: {$user->name} (ID: {$user->id})\n";
    
    // Vérifier les rôles via la table pivot
    if (DB::connection()->getSchemaBuilder()->hasTable('user_role')) {
        $roles = DB::table('user_role')
            ->join('roles', 'user_role.role_id', '=', 'roles.id')
            ->where('user_role.user_id', $user->id)
            ->select('roles.name', 'roles.id')
            ->get();
        
        if (count($roles) > 0) {
            echo "- Rôles: ";
            foreach ($roles as $role) {
                echo "{$role->name} (ID: {$role->id}), ";
            }
            echo "\n";
        } else {
            echo "- Aucun rôle assigné ⚠️\n";
        }
    }
    echo "\n";
}

// Vérifier les migrations
echo "=== ÉTAT DES MIGRATIONS ===\n\n";
$migrations = DB::table('migrations')->orderBy('id', 'desc')->limit(10)->get();
foreach ($migrations as $migration) {
    echo "{$migration->id} | {$migration->migration} | Batch: {$migration->batch}\n";
}
