<?php

namespace Database\Factories;

use App\Models\Fournisseur;
use Illuminate\Database\Eloquent\Factories\Factory;

class FournisseurFactory extends Factory
{
    protected $model = Fournisseur::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->company,
'code' => strtoupper($this->faker->bothify('FRN-####')),
'contact_principal' => $this->faker->name,
'telephone' => $this->faker->phoneNumber,
'email' => $this->faker->unique()->safeEmail,
'adresse' => $this->faker->address,
'ville' => $this->faker->city,
'pays' => $this->faker->country,
'ninea' => $this->faker->optional()->numerify('########'),
'registre_commerce' => $this->faker->optional()->numerify('RC#######'),
'notes' => $this->faker->optional()->sentence,
'solde_actuel' => $this->faker->randomFloat(2, 0, 1000000),
'statut' => $this->faker->randomElement(['actif', 'inactif']),
'actif' => $this->faker->boolean(90),
        ];
    }
}
