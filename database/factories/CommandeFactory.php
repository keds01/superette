<?php

namespace Database\Factories;

use App\Models\Commande;
use App\Models\Fournisseur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommandeFactory extends Factory
{
    protected $model = Commande::class;

    public function definition()
    {
        return [
            'numero_commande' => 'CMD-' . Str::upper(Str::random(8)),
            'fournisseur_id' => Fournisseur::inRandomOrder()->first()?->id ?? Fournisseur::factory(),
            'date_commande' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'date_livraison_prevue' => $this->faker->dateTimeBetween('now', '+1 month'),
            'statut' => $this->faker->randomElement(['en_attente', 'validee', 'partiellement_recue', 'recue', 'annulee']),
            'montant_total' => 0, // Sera calculé après la création des détails
            'devise' => 'FCFA',
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Commande $commande) {
            // Le montant total sera mis à jour dans le seeder après la création des détails.
        });
    }
}
