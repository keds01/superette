<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Categorie;

class CategoryFactory extends Factory
{
    protected $model = Categorie::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'parent_id' => Categorie::factory(),
        ];
    }
} 