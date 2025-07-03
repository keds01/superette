<?php

namespace Database\Factories;

use App\Models\DetailVente;
use App\Models\Produit;
use App\Models\Vente;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailVenteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DetailVente::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Assurez-vous qu'il existe des produits
        $produit = Produit::inRandomOrder()->first() ?? Produit::factory()->create();

        $quantite = $this->faker->numberBetween(1, 5);
        $prixUnitaire = $produit->prix_vente_ttc;

        return [
            'vente_id' => Vente::factory(),
            'produit_id' => $produit->id,
            'quantite' => $quantite,
            'prix_unitaire' => $prixUnitaire,
            'sous_total' => $quantite * $prixUnitaire,
        ];
    }
} 