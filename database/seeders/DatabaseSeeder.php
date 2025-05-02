<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Utilisateur::create([
            'nom' => 'rayen',
            'prenom' => 'bensalem',
            'email' => 'rayenbsm03@gmail.com',
            'password' => Hash::make('password123'),
            'statut' => true,
        ]);
    }
}
