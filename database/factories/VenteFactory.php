<?php

namespace Database\Factories;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Employe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vente::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Assurez-vous qu'il existe des clients et des employés
        $clientId = Client::inRandomOrder()->first()->id ?? Client::factory()->create()->id;
        
        // Utiliser Employe si disponible, sinon User
        if (class_exists(Employe::class) && Employe::count() > 0) {
            $employeId = Employe::inRandomOrder()->first()->id;
        } else {
            $employeId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;
        }

        $statuts = ['en_cours', 'terminee', 'annulee'];
        $montantTotal = $this->faker->randomFloat(2, 1000, 50000);
        $statut = $this->faker->randomElement($statuts);

        $montantPaye = 0;
        if ($statut === 'terminee') {
            $montantPaye = $this->faker->randomElement([$montantTotal, $this->faker->randomFloat(2, 0, $montantTotal)]);
        } elseif ($statut === 'en_cours') {
            $montantPaye = $this->faker->randomFloat(2, 0, $montantTotal / 2);
        }

        // Déterminer la superette associée (prendre celle du client, de l'employé ou par défaut 1)
        $superetteId = 1;
        if (class_exists(Client::class) && $clientId) {
            $client = Client::find($clientId);
            if ($client && $client->superette_id) {
                $superetteId = $client->superette_id;
            }
        }
        if (class_exists(Employe::class) && $employeId) {
            $employe = Employe::find($employeId);
            if ($employe && $employe->superette_id) {
                $superetteId = $employe->superette_id;
            }
        }

        return [
            'numero_vente' => 'V-' . $this->faker->unique()->numerify('##########'),
            'client_id' => $clientId,
            'employe_id' => $employeId,
            'superette_id' => $superetteId,
            'date_vente' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'type_vente' => $this->faker->randomElement(['sur_place', 'livraison']),
            'montant_total' => $montantTotal,
            'montant_paye' => $montantPaye,
            'montant_restant' => $montantTotal - $montantPaye,
            'statut' => $statut,
            'notes' => $this->faker->sentence(),
        ];
    }
} 