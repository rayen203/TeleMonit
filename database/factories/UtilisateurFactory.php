<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Utilisateur;

class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), // Le mutateur setPasswordAttribute va hacher cela
            'last_activity' => now(),
            'statut' => $this->faker->boolean(),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}
