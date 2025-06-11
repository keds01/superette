<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProduitApiController;
use App\Http\Controllers\Api\ProduitSearchController;

Route::get('/produits/search', ProduitSearchController::class)->name('api.produits.search');

// Route pour rechercher un produit par code-barres
Route::get('/produits/barcode/{barcode}', [ProduitApiController::class, 'searchByBarcode']);

Route::get('/user', function (Request $request) {
    return $request->user();
});

// Ajoutez cette ligne pour la vérification de stock
Route::get('/products/{product}/check-stock', [ProduitApiController::class, 'checkStock']);