<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'code' => 'CLT-' . strtoupper($this->faker->unique()->bothify('??###')),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'telephone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'adresse' => $this->faker->address(),
            'notes' => $this->faker->optional()->sentence(),
            'statut' => $this->faker->randomElement(['actif', 'inactif']),
        ];
    }
} 