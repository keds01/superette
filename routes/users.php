<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Routes pour la gestion des utilisateurs
Route::middleware(['auth'])->group(function () {
    // Interface d'administration unifiée
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');
    Route::get('/users/{id}/roles', [\App\Http\Controllers\AdminController::class, 'getUserRoles'])->name('admin.getUserRoles');
    Route::get('/roles/{id}/permissions', [\App\Http\Controllers\AdminController::class, 'getRolePermissions'])->name('admin.getRolePermissions');
    
    // Routes pour les utilisateurs
    Route::resource('users', UserController::class);
    
    // Routes pour les rôles et permissions
    Route::resource('roles', RoleController::class);
});
