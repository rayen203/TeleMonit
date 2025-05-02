<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Utilisateur;
use App\Models\Teletravailleur;
use App\Models\Administrateur;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $teletravailleurUser;
    protected $adminUser;
    protected $teletravailleur;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur pour le télétravailleur
        $this->teletravailleurUser = Utilisateur::factory()->create([
            'nom' => 'Teletravailleur',
            'prenom' => 'Test',
            'email' => 'teletravailleur@test.com',
            'password' => 'password', // Le mutateur setPasswordAttribute va hacher cela
            'last_activity' => now(),
            'statut' => true,
        ]);

        // Créer un télétravailleur
        $this->teletravailleur = Teletravailleur::factory()->create([
            'user_id' => $this->teletravailleurUser->id,
            'telephone' => '+216 12 345 678',
            'adresse' => '123 Rue Exemple, Tunis',
            'photoProfil' => 'https://example.com/photo.jpg',
            'CIN' => '87654321',
            'token' => 'teletravailleur-token-123',
        ]);

        // Créer un utilisateur pour l'admin
        $this->adminUser = Utilisateur::factory()->create([
            'nom' => 'Admin',
            'prenom' => 'Test',
            'email' => 'admin@test.com',
            'password' => 'password', // Le mutateur setPasswordAttribute va hacher cela
            'last_activity' => now(),
            'statut' => true,
        ]);

        // Créer un admin
        $this->admin = Administrateur::factory()->create([
            'user_id' => $this->adminUser->id,
        ]);

        // Charger la relation administrateur pour s'assurer qu'elle est bien définie
        $this->adminUser->load('administrateur');
    }
}
