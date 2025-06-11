<?php

require '../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h1>Vérification des tables de permissions</h1>";

// Vérifier si les tables existent
$tables = ['roles', 'permissions', 'role_permission', 'user_role'];
foreach ($tables as $table) {
    $exists = DB::connection()->getSchemaBuilder()->hasTable($table);
    echo "<p>Table '$table': " . ($exists ? "Existe ✅" : "N'existe pas ❌") . "</p>";
    
    if ($exists) {
        $count = DB::table($table)->count();
        echo "<p>- Nombre d'enregistrements: $count</p>";
        
        if ($count > 0) {
            echo "<p>- Premiers enregistrements:</p>";
            echo "<pre>";
            print_r(DB::table($table)->limit(5)->get()->toArray());
            echo "</pre>";
        }
    }
}

// Vérifier si nous avons un utilisateur admin avec son rôle
echo "<h2>Utilisateurs et leurs rôles</h2>";
$users = DB::table('users')->get();
foreach ($users as $user) {
    echo "<p>Utilisateur: {$user->name} (ID: {$user->id})</p>";
    
    // Vérifier les rôles via la table pivot
    if (DB::connection()->getSchemaBuilder()->hasTable('user_role')) {
        $roles = DB::table('user_role')
            ->join('roles', 'user_role.role_id', '=', 'roles.id')
            ->where('user_role.user_id', $user->id)
            ->select('roles.name', 'roles.id')
            ->get();
        
        if (count($roles) > 0) {
            echo "<p>- Rôles: ";
            foreach ($roles as $role) {
                echo "{$role->name} (ID: {$role->id}), ";
            }
            echo "</p>";
        } else {
            echo "<p>- Aucun rôle assigné ⚠️</p>";
        }
    }
}

// Vérifier les migrations
echo "<h2>État des migrations</h2>";
$migrations = DB::table('migrations')->orderBy('id', 'desc')->get();
echo "<pre>";
foreach ($migrations as $migration) {
    echo "{$migration->id} | {$migration->migration} | Batch: {$migration->batch}\n";
}
echo "</pre>";
