<?php

namespace Database\Factories;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MouvementStockFactory extends Factory
{
    protected $model = MouvementStock::class;

    public function definition()
    {
        $produit = Produit::inRandomOrder()->first() ?? Produit::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $superetteId = $produit->superette_id ?? 1;
        $type = $this->faker->randomElement(['entree', 'sortie', 'ajustement', 'inventaire']);
        $quantite = $this->faker->numberBetween(1, 100);
        $stockAvant = $this->faker->numberBetween(0, 200);
        $stockApres = $stockAvant + ($type === 'entree' ? $quantite : -$quantite);
        return [
            'produit_id' => $produit->id,
            'type' => $type,
            'quantite_avant_conditionnement' => $stockAvant,
            'quantite_avant_unite' => $stockAvant,
            'quantite_apres_conditionnement' => $stockApres,
            'quantite_apres_unite' => $stockApres,
            'date_peremption' => null,
            'reference_mouvement' => null,
            'type_reference' => null,
            'motif' => $this->faker->sentence(),
            'user_id' => $user->id,
            'date_mouvement' => now(),
            'superette_id' => $superetteId
        ];
    }
} 