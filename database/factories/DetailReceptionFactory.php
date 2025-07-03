<?php

namespace Database\Factories;

use App\Models\DetailReception;
use App\Models\Produit;
use App\Models\Reception;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailReceptionFactory extends Factory
{
    protected $model = DetailReception::class;

    public function definition()
    {
        $datePeremption = null;
        if ($this->faker->boolean(70)) { // 70% de chance d'avoir une date de péremption
            $datePeremption = $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d');
        }

        return [
            'reception_id' => Reception::factory(), // Sera généralement surchargé
            'produit_id' => Produit::inRandomOrder()->first()?->id ?? Produit::factory(),
            'quantite' => $this->faker->numberBetween(1, 100),
            'prix_unitaire' => $this->faker->randomFloat(2, 100, 10000),
            'date_peremption' => $datePeremption,
        ];
    }
}