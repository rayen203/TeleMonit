<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Administrateur;
use App\Models\Utilisateur;

class AdministrateurFactory extends Factory
{
    protected $model = Administrateur::class;

    public function definition()
    {
        return [
            'user_id' => Utilisateur::factory(), // Crée un Utilisateur associé
        ];
    }
}
