<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produit; // Correction du nom du modèle
use Illuminate\Support\Facades\Log;

class ProduitApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function searchByBarcode($barcode)
    {
        try {
            $produit = Product::where('code_barres', $barcode)->first();

            if ($produit) {
                return response()->json([
                    'success' => true,
                    'produit' => $produit
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche par code-barres', [
                'barcode' => $barcode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur lors de la recherche du produit.'
            ], 500);
        }
    }
}
