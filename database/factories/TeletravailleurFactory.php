<?php

namespace Database\Factories;

use App\Models\Teletravailleur;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeletravailleurFactory extends Factory
{
    protected $model = Teletravailleur::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\Utilisateur::factory(),
            'telephone' => '+216 12 345 678',
            'adresse' => '123 Rue Exemple, Tunis',
            'photoProfil' => 'https://example.com/photo.jpg',
            'CIN' => $this->faker->unique()->numerify('########'), // Génère un CIN unique (8 chiffres)
            'token' => 'teletravailleur-token-' . uniqid(),
        ];
    }
}
