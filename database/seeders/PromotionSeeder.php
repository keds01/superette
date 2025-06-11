<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Produit;
use App\Models\Promotion;

class PromotionSeeder extends Seeder
{
    /**
     * Crée une promotion active sur le produit "riz" si non existante
     */
    public function run(): void
    {
        // Recherche du produit "riz" (nom ou slug)
        $produit = Produit::where('nom', 'like', '%riz%')->first();

        if (!$produit) {
            Log::warning('[PromotionSeeder] Aucun produit "riz" trouvé, promotion non créée.');
            return;
        }

        // Vérifie si une promotion active existe déjà sur ce produit
        $promotionExistante = Promotion::where('produit_id', $produit->id)
            ->where('actif', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->first();

        if ($promotionExistante) {
            Log::info('[PromotionSeeder] Promotion déjà existante pour le produit riz.');
            return;
        }

        // Création d'une promotion de -500 FCFA valable 1 mois
        Promotion::create([
            'produit_id'   => $produit->id,
            'type'         => 'montant',
            'valeur'       => 500,
            'date_debut'   => Carbon::now(),
            'date_fin'     => Carbon::now()->addMonth(),
            'actif'        => true,
            'description'  => 'Promo spéciale riz -500 FCFA',
        ]);
    }
}
