<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Utilisateur;
use App\Models\Teletravailleur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TeletravailleurCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un admin (sans rôle, car la table utilisateurs n'a pas de colonne "role")
        $this->admin = Utilisateur::factory()->create([
            'nom' => 'Admin',
            'prenom' => 'Test',
            'email' => 'admin' . Str::random(8) . '@test.com',
            'password' => Hash::make('password'),
            'statut' => true,
        ]);
    }

    /** @test */
    public function admin_can_create_teletravailleur()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson('/admin/teletravailleurs', [
            'nom' => 'Alice',
            'prenom' => 'Smith',
            'email' => 'alice.smith' . Str::random(8) . '@test.com',
            'CIN' => '12345678',
            'telephone' => '+21612345678',
            'adresse' => '123 Rue Exemple, Tunis',
            'photoProfil' => 'https://example.com/photo.jpg',
        ]);

        $response->assertStatus(201);

        // Vérifier que l'utilisateur est créé
        $this->assertDatabaseHas('utilisateurs', [
            'email' => $response->json('teletravailleur.email'),
        ]);

        // Vérifier que l'entrée dans teletravailleurs est créée
        $this->assertDatabaseHas('teletravailleurs', [
            'CIN' => '12345678',
            'telephone' => '+21612345678',
            'adresse' => '123 Rue Exemple, Tunis',
            'photoProfil' => 'https://example.com/photo.jpg',
        ]);
    }

    /** @test */
    public function admin_can_update_teletravailleur()
    {
        // Créer un utilisateur pour le télétravailleur
        $teletravailleurUser = Utilisateur::factory()->create([
            'email' => 'teletravailleur' . Str::random(8) . '@test.com',
            'statut' => true,
        ]);

        $teletravailleur = Teletravailleur::factory()->create([
            'user_id' => $teletravailleurUser->id,
            'CIN' => '12345678' . Str::random(4),
            'telephone' => '+21612345678',
            'adresse' => '123 Rue Exemple, Tunis',
            'photoProfil' => 'https://example.com/photo.jpg',
        ]);

        $this->actingAs($this->admin);

        $response = $this->putJson("/admin/teletravailleurs/{$teletravailleur->id}", [
            'nom' => 'Alice Updated',
            'prenom' => 'Smith Updated',
            'email' => 'alice.updated' . Str::random(8) . '@test.com',
            'CIN' => '87654321',
            'telephone' => '+21687654321',
            'adresse' => '456 Avenue Exemple, Tunis',
            'photoProfil' => 'https://example.com/newphoto.jpg',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('utilisateurs', [
            'id' => $teletravailleurUser->id,
            'nom' => 'Alice Updated',
            'prenom' => 'Smith Updated',
            'email' => $response->json('teletravailleur.email'),
        ]);
        $this->assertDatabaseHas('teletravailleurs', [
            'id' => $teletravailleur->id,
            'CIN' => '87654321',
            'telephone' => '+21687654321',
            'adresse' => '456 Avenue Exemple, Tunis',
            'photoProfil' => 'https://example.com/newphoto.jpg',
        ]);
    }

    /** @test */
    public function admin_can_delete_teletravailleur()
    {
        $teletravailleurUser = Utilisateur::factory()->create([
            'email' => 'teletravailleur' . Str::random(8) . '@test.com',
            'statut' => true,
        ]);

        $teletravailleur = Teletravailleur::factory()->create([
            'user_id' => $teletravailleurUser->id,
            'CIN' => '12345678' . Str::random(4),
        ]);

        $this->actingAs($this->admin);

        $response = $this->deleteJson("/admin/teletravailleurs/{$teletravailleur->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('utilisateurs', [
            'id' => $teletravailleurUser->id,
        ]);
        $this->assertDatabaseMissing('teletravailleurs', [
            'id' => $teletravailleur->id,
        ]);
    }

    /** @test */
    public function teletravailleur_status_becomes_active_on_login()
    {
        $teletravailleurUser = Utilisateur::factory()->create([
            'email' => 'teletravailleur' . Str::random(8) . '@test.com',
            'password' => Hash::make('password'),
            'statut' => false,
        ]);

        Teletravailleur::factory()->create([
            'user_id' => $teletravailleurUser->id,
            'CIN' => '12345678' . Str::random(4),
        ]);

        $response = $this->post('/login', [
            'email' => $teletravailleurUser->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($teletravailleurUser);
        $this->assertDatabaseHas('utilisateurs', [
            'id' => $teletravailleurUser->id,
            'statut' => true,
        ]);
    }
}
