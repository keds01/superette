<?php

namespace Database\Factories;

use App\Models\Fournisseur;
use App\Models\Reception;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReceptionFactory extends Factory
{
    protected $model = Reception::class;

    public function definition()
    {
        return [
            'numero' => 'REC-' . Str::upper(Str::random(8)),
            'fournisseur_id' => Fournisseur::inRandomOrder()->first()?->id ?? Fournisseur::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'date_reception' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'numero_facture' => $this->faker->optional()->ean13,
            'mode_paiement' => $this->faker->randomElement(['especes', 'cheque', 'virement', 'autre']),
            'description' => $this->faker->optional()->sentence,
            'statut' => $this->faker->randomElement(['en_cours', 'terminee', 'annulee']),
        ];
    }
}
