<?php

namespace Database\Factories;

use App\Models\DetailCommande;
use App\Models\Produit;
use App\Models\Commande;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailCommandeFactory extends Factory
{
    protected $model = DetailCommande::class;

    public function definition()
    {
        $quantite = $this->faker->numberBetween(1, 50);
        $prix_unitaire = $this->faker->randomFloat(2, 500, 20000);

        return [
            'commande_id' => Commande::factory(), // Sera généralement surchargé
            'produit_id' => Produit::inRandomOrder()->first()?->id ?? Produit::factory(),
            'quantite' => $quantite,
            'prix_unitaire' => $prix_unitaire,
            'montant_total' => $quantite * $prix_unitaire,
        ];
    }
}
