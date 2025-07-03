<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Produit;
use App\Models\Unite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produit>
 */
class ProduitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prix_achat_ht = $this->faker->randomFloat(2, 100, 5000);
        $marge = $this->faker->randomFloat(2, 10, 50);
        $tva = $this->faker->randomElement([0, 5.5, 10, 20]);
        $prix_vente_ht = $prix_achat_ht * (1 + $marge / 100);
        $prix_vente_ttc = $prix_vente_ht * (1 + $tva / 100);

        return [
            'nom' => $this->faker->words(3, true),
            'reference' => $this->faker->unique()->ean8(),
            'code_barres' => $this->faker->unique()->ean13(),
            'description' => $this->faker->sentence(),
            'categorie_id' => Categorie::inRandomOrder()->first()->id ?? Categorie::factory()->create()->id,
            'unite_vente_id' => Unite::inRandomOrder()->first()->id ?? Unite::factory()->create()->id,
            'stock' => $this->faker->numberBetween(0, 200),
            'seuil_alerte' => $this->faker->numberBetween(5, 20),
            'prix_achat_ht' => $prix_achat_ht,
            'prix_vente_ht' => $prix_vente_ht,
            'prix_vente_ttc' => $prix_vente_ttc,
            'marge' => $marge,
            'tva' => $tva,
            'actif' => true,
        ];
    }
}