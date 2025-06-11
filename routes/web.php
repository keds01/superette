<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\RemiseController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\DocumentEmployeController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeController;
use App\Models\Remise;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaisseAuthController;

// Routes d'authentification - accessibles sans login
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);

// Toutes les autres routes nécessitent d'être connecté
Route::middleware(['auth'])->group(function () {
    // Route par défaut
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Routes des produits sans restriction
    // On redirige l'index des produits vers les stocks, mais on garde les autres routes
    Route::get('/produits', function() { return redirect('/stocks'); })->name('produits.index');
    
    // Routes des produits (sauf index qui est redirigé)
    Route::resource('produits', ProduitController::class)->except(['index']);
    // Export des produits
    // Route dupliquée supprimée pour éviter conflit de nom
    // Route::get('/export-produits-excel', [ProduitController::class, 'exportExcel'])->name('produits.export.excel');
    // Route::get('/export-produits-pdf', [ProduitController::class, 'exportPdf'])->name('produits.export.pdf');
    Route::get('/produits/export/excel', [ProduitController::class, 'exportExcel'])->name('produits.export.excel');
    Route::get('/produits/export/pdf', [ProduitController::class, 'exportPdf'])->name('produits.export.pdf');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Catégories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{categorie}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{categorie}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{categorie}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{categorie}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Unités
    Route::resource('unites', UnitController::class);

    // Mouvements de stock - protection par middleware de rôles
    
        Route::resource('mouvements-stock', StockMovementController::class)->except(['edit', 'update']);
        Route::get('/ajax/produit/{id}/details', [StockMovementController::class, 'getProductDetails'])->name('produit.details.ajax');

    Route::get('/ajax/produit/{id}/details-promotion', [PromotionController::class, 'getProductDetails'])->name('ajax.produit.details-promotion');

    // Alertes
    Route::prefix('alertes')->name('alertes.')->group(function () {
        Route::get('/', [AlerteController::class, 'index'])->name('index');
        Route::get('/create', [AlerteController::class, 'create'])->name('create');
        Route::post('/', [AlerteController::class, 'store'])->name('store');
        Route::put('/{alerte}', [AlerteController::class, 'update'])->name('update');
        Route::delete('/{alerte}', [AlerteController::class, 'destroy'])->name('destroy');
        Route::get('/verifier', [AlerteController::class, 'verifierAlertes'])->name('verifier');
    });

    // Rapports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/movements', [ReportController::class, 'movements'])->name('movements');
        Route::get('/categories', [ReportController::class, 'categories'])->name('categories');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });

    // Administration
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('index');
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}', [\App\Http\Controllers\AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'destroyUser'])->name('users.destroy');

        // Permissions CRUD complet avec noms admin.permissions.*
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PermissionController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\PermissionController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\PermissionController::class, 'store'])->name('store');
            Route::get('/{permission}', [\App\Http\Controllers\PermissionController::class, 'show'])->name('show');
            Route::get('/{permission}/edit', [\App\Http\Controllers\PermissionController::class, 'edit'])->name('edit');
            Route::put('/{permission}', [\App\Http\Controllers\PermissionController::class, 'update'])->name('update');
            Route::delete('/{permission}', [\App\Http\Controllers\PermissionController::class, 'destroy'])->name('destroy');
        });

        // Rôles CRUD complet avec noms admin.roles.*
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [\App\Http\Controllers\RoleController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\RoleController::class, 'store'])->name('store');
            Route::get('/{role}', [\App\Http\Controllers\RoleController::class, 'show'])->name('show');
            Route::get('/{role}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('destroy');
        });
    });

    // Routes pour les permissions
    Route::get('/permissions', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [\App\Http\Controllers\AdminController::class, 'createPermission'])->name('permissions.create');
    Route::post('/permissions', [\App\Http\Controllers\AdminController::class, 'storePermission'])->name('permissions.store');
    Route::get('/permissions/{permission}', [\App\Http\Controllers\PermissionController::class, 'show'])->name('permissions.show');
    Route::get('/permissions/{permission}/edit', [\App\Http\Controllers\PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [\App\Http\Controllers\PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [\App\Http\Controllers\PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::put('/users/{user}/roles', [\App\Http\Controllers\AdminController::class, 'updateRoles'])->name('users.roles.update');
    Route::put('/roles/{role}/permissions', [\App\Http\Controllers\AdminController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::put('/users/{user}/toggle-status', [\App\Http\Controllers\AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::get('/users/{user}/roles', [\App\Http\Controllers\AdminController::class, 'getUserRoles'])->name('users.roles.get');
    Route::get('/roles/{role}/permissions', [\App\Http\Controllers\AdminController::class, 'getRolePermissions'])->name('roles.permissions.get');

    // CRUD Remises (admin)
    Route::resource('remises', \App\Http\Controllers\RemiseController::class);

    // Users et Roles CRUD
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class);

    // Ventes
    Route::prefix('ventes')->name('ventes.')->group(function () {
        Route::get('/', [VenteController::class, 'index'])->name('index');
        Route::get('/create', [VenteController::class, 'create'])->name('create');
        Route::post('/', [VenteController::class, 'store'])->name('store'); 
        Route::get('/{vente}', [VenteController::class, 'show'])->name('show');
        Route::get('/{vente}/edit', [VenteController::class, 'edit'])->name('edit');
        Route::put('/{vente}', [VenteController::class, 'update'])->name('update');
        Route::delete('/{vente}', [VenteController::class, 'destroy'])->name('destroy');
        Route::get('/{vente}/facture', [VenteController::class, 'imprimerFacture'])->name('facture');
        Route::get('/{vente}/recu', [VenteController::class, 'recu'])->name('recu');
    });

    // Route spéciale pour l'enregistrement simplifié des ventes (contourne les validations)
    Route::post('/ventes-express', [VenteController::class, 'storeExpress'])->name('ventes.express');

    // Paiements
    Route::prefix('paiements')->name('paiements.')->group(function () {
        Route::get('/', [App\Http\Controllers\PaiementController::class, 'index'])->name('index');
        Route::post('/ventes/{vente}', [App\Http\Controllers\PaiementController::class, 'store'])->name('paiements.store');
        Route::post('/{paiement}/valider', [App\Http\Controllers\PaiementController::class, 'valider'])->name('valider');
        Route::post('/{paiement}/refuser', [App\Http\Controllers\PaiementController::class, 'refuser'])->name('refuser');
        Route::post('/{paiement}/rembourser', [App\Http\Controllers\PaiementController::class, 'rembourser'])->name('rembourser');
        Route::delete('/{paiement}', [App\Http\Controllers\PaiementController::class, 'destroy'])->name('destroy');
    });

    // Retours
    Route::prefix('retours')->name('retours.')->group(function () {
        Route::get('/', [App\Http\Controllers\RetourVenteController::class, 'index'])->name('index');
        Route::get('/ventes/{vente}/create', [App\Http\Controllers\RetourVenteController::class, 'create'])->name('retours.create');
        Route::post('/ventes/{vente}', [App\Http\Controllers\RetourVenteController::class, 'store'])->name('retours.store');
        Route::get('/{retour}', [App\Http\Controllers\RetourVenteController::class, 'show'])->name('retours.show');
        Route::post('/{retour}/terminer', [App\Http\Controllers\RetourVenteController::class, 'terminer'])->name('terminer');
        Route::post('/{retour}/annuler', [App\Http\Controllers\RetourVenteController::class, 'annuler'])->name('annuler');
        Route::delete('/{retour}', [App\Http\Controllers\RetourVenteController::class, 'destroy'])->name('retours.destroy');
    });
    Route::post('/ventes/{vente}', [App\Http\Controllers\PaiementController::class, 'store'])->name('paiements.store');
    Route::post('/{paiement}/valider', [App\Http\Controllers\PaiementController::class, 'valider'])->name('valider');
    Route::post('/{paiement}/refuser', [App\Http\Controllers\PaiementController::class, 'refuser'])->name('refuser');
    Route::post('/{paiement}/rembourser', [App\Http\Controllers\PaiementController::class, 'rembourser'])->name('rembourser');
    Route::delete('/{paiement}', [App\Http\Controllers\PaiementController::class, 'destroy'])->name('destroy');


// Retours
Route::prefix('retours')->name('retours.')->group(function () {
    Route::get('/', [App\Http\Controllers\RetourVenteController::class, 'index'])->name('index');
    Route::get('/ventes/{vente}/create', [App\Http\Controllers\RetourVenteController::class, 'create'])->name('retours.create');
    Route::post('/ventes/{vente}', [App\Http\Controllers\RetourVenteController::class, 'store'])->name('retours.store');
    Route::get('/{retour}', [App\Http\Controllers\RetourVenteController::class, 'show'])->name('retours.show');
    Route::post('/{retour}/terminer', [App\Http\Controllers\RetourVenteController::class, 'terminer'])->name('terminer');
    Route::post('/{retour}/annuler', [App\Http\Controllers\RetourVenteController::class, 'annuler'])->name('annuler');
    Route::delete('/{retour}', [App\Http\Controllers\RetourVenteController::class, 'destroy'])->name('retours.destroy');
});

// Remises
Route::prefix('remises')->name('remises.')->group(function () {
    // Restauration de la route index avec une version simplifiée
    Route::get('/', function() {
        // Version simplifiée pour résoudre le problème
        $remises = Remise::orderBy('created_at', 'desc')->paginate(10);
        return view('remises.index', compact('remises'));
    })->name('index');
    Route::get('/create', [RemiseController::class, 'create'])->name('remises.create');
    Route::post('/ventes/{vente}', [RemiseController::class, 'store'])->name('remises.store');
    Route::put('/{remise}', [RemiseController::class, 'update'])->name('remises.update');
    Route::delete('/{remise}', [RemiseController::class, 'destroy'])->name('remises.destroy');
    Route::post('/verifier-code', [RemiseController::class, 'verifierCode'])->name('verifier-code');
});

// Documents employés
Route::prefix('documents-employes')->name('documents-employes.')->group(function () {
    Route::get('/', [DocumentEmployeController::class, 'index'])->name('index');
    Route::get('/create/{employe}', [DocumentEmployeController::class, 'create'])->name('documents-employes.create');
    Route::post('/{employe}', [DocumentEmployeController::class, 'store'])->name('documents-employes.store');
    Route::get('/{document}', [DocumentEmployeController::class, 'show'])->name('documents-employes.show');
    Route::get('/{document}/edit', [DocumentEmployeController::class, 'edit'])->name('documents-employes.edit');
    Route::put('/{document}', [DocumentEmployeController::class, 'update'])->name('documents-employes.update');
    Route::delete('/{document}', [DocumentEmployeController::class, 'destroy'])->name('documents-employes.destroy');
    Route::get('/{document}/download', [DocumentEmployeController::class, 'download'])->name('download');
});

// Stocks
Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');

// Fournisseurs
Route::resource('fournisseurs', FournisseurController::class);

// Commandes fournisseurs
Route::get('/commandes', [\App\Http\Controllers\CommandeController::class, 'index'])->name('commandes.index');
Route::get('/commandes/create', [\App\Http\Controllers\CommandeController::class, 'create'])->name('commandes.create');
Route::post('/commandes', [\App\Http\Controllers\CommandeController::class, 'store'])->name('commandes.store');
Route::get('/commandes/{commande}', [\App\Http\Controllers\CommandeController::class, 'show'])->name('commandes.show');
Route::get('/commandes/{commande}/edit', [\App\Http\Controllers\CommandeController::class, 'edit'])->name('commandes.edit');
Route::put('/commandes/{commande}', [\App\Http\Controllers\CommandeController::class, 'update'])->name('commandes.update');
Route::delete('/commandes/{commande}', [\App\Http\Controllers\CommandeController::class, 'destroy'])->name('commandes.destroy');

// Réceptions de marchandises
Route::get('/receptions', [\App\Http\Controllers\ReceptionController::class, 'index'])->name('receptions.index');
Route::get('/receptions/create', [\App\Http\Controllers\ReceptionController::class, 'create'])->name('receptions.create');
Route::post('/receptions', [\App\Http\Controllers\ReceptionController::class, 'store'])->name('receptions.store');
Route::get('/receptions/{reception}', [\App\Http\Controllers\ReceptionController::class, 'show'])->name('receptions.show');
Route::get('/receptions/{reception}/edit', [\App\Http\Controllers\ReceptionController::class, 'edit'])->name('receptions.edit');
Route::put('/receptions/{reception}', [\App\Http\Controllers\ReceptionController::class, 'update'])->name('receptions.update');
Route::delete('/receptions/{reception}', [\App\Http\Controllers\ReceptionController::class, 'destroy'])->name('receptions.destroy');

// Promotions
Route::prefix('promotions')->name('promotions.')->group(function () {
    Route::get('/', [PromotionController::class, 'index'])->name('index');
    Route::get('/create', [PromotionController::class, 'create'])->name('create');
    Route::post('/', [PromotionController::class, 'store'])->name('store');
    Route::get('/{promotion}/edit', [PromotionController::class, 'edit'])->name('edit');
    Route::put('/{promotion}', [PromotionController::class, 'update'])->name('update');
    Route::delete('/{promotion}', [PromotionController::class, 'destroy'])->name('destroy');
});

// Routes pour la caisse
Route::get('/caisse', [CaisseController::class, 'index'])->name('caisse.index');
Route::post('/caisse', [CaisseController::class, 'store'])->name('caisse.store');
Route::get('/caisse/{vente}', [CaisseController::class, 'show'])->name('caisse.show');
Route::get('/caisse/{caisse}/recu', [CaisseController::class, 'imprimerRecuOperation'])->name('caisse.imprimerRecuOperation');
Route::get('/caisse/rapport', [CaisseController::class, 'rapport'])->name('caisse.rapport');

// Routes pour l'audit
Route::prefix('audit')->name('audit.')->group(function () {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/', [AuditController::class, 'index'])->name('index');
    Route::get('/journal', [AuditController::class, 'journal'])->name('journal');
    
    // Routes qui nécessitent des rôles spécifiques
    Route::get('/journal/export', [AuditController::class, 'exporterJournal'])->name('exporter-journal');
    Route::get('/anomalies', [AuditController::class, 'anomalies'])->name('anomalies');
    Route::get('/anomalies/export', [AuditController::class, 'exporterAnomalies'])->name('exporter-anomalies');
    Route::get('/anomalies/{id}', [AuditController::class, 'detailAnomalie'])->name('detail-anomalie');
    Route::post('/anomalies/{id}/en-cours', [AuditController::class, 'marquerEnCours'])->name('marquer-en-cours');
    Route::post('/anomalies/{id}/resolue', [AuditController::class, 'marquerResolue'])->name('marquer-resolue');
    Route::post('/anomalies/{id}/ignorer', [AuditController::class, 'ignorerAnomalie'])->name('ignorer-anomalie');
    Route::post('/anomalies/{id}/commentaire', [AuditController::class, 'ajouterCommentaire'])->name('ajouter-commentaire');
    Route::get('/rapport-quotidien', [AuditController::class, 'rapportQuotidien'])->name('rapport-quotidien');
});

// Clients
Route::resource('clients', ClientController::class);

// Employés
Route::resource('employes', EmployeController::class);

// Routes pour la gestion des statisques
Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

// Routes pour l'authentification des caisses
// Route::prefix('caisse')->name('caisse.')->group(function () {
//     Route::get('/login', [CaisseAuthController::class, 'showLoginForm'])->name('login');
//     Route::post('/login', [CaisseAuthController::class, 'login']);
//     Route::post('/logout', [CaisseAuthController::class, 'logout'])->name('logout');
//     Route::get('/dashboard', [CaisseAuthController::class, 'dashboard'])->name('dashboard');
//     Route::get('/check-session', [CaisseAuthController::class, 'checkSession'])->name('check-session');
// });

// Routes API pour la gestion des produits
Route::get('/api/produits/{id}', [\App\Http\Controllers\Api\ApiProduitController::class, 'show']);
Route::get('/api/produits/barcode/{barcode}', [\App\Http\Controllers\Api\ProduitSearchController::class, 'searchByBarcode']);

// Catch-all route (doit être la dernière)
// Route::fallback(function() {
//     return view('errors.404'); // Ou une vue d'erreur 404 personnalisée
// });
}); // Fin groupe auth
