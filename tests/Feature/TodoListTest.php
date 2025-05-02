<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Utilisateur;
use App\Models\Teletravailleur;
use App\Models\Calendar; // Supposons un modèle Task pour les tâches
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TodoListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teletravailleur;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un admin (sans rôle)
        $this->admin = Utilisateur::factory()->create([
            'email' => 'admin' . Str::random(8) . '@test.com',
            'password' => Hash::make('password'),
            'statut' => true,
        ]);

        // Créer un télétravailleur
        $teletravailleurUser = Utilisateur::factory()->create([
            'email' => 'teletravailleur' . Str::random(8) . '@test.com',
            'password' => Hash::make('password'),
            'statut' => true,
        ]);

        $this->teletravailleur = Teletravailleur::factory()->create([
            'user_id' => $teletravailleurUser->id,
            'CIN' => '12345678' . Str::random(4),
        ]);
    }

    /** @test */
    public function admin_can_create_and_assign_task()
    {
        $this->actingAs($this->admin);

        $response = $this->postJson('/admin/tasks', [
            'title' => 'Développer une fonctionnalité',
            'description' => 'Créer une nouvelle page',
            'deadline' => now()->addDays(5)->toDateString(),
            'teletravailleur_id' => $this->teletravailleur->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Développer une fonctionnalité',
            'teletravailleur_id' => $this->teletravailleur->id,
        ]);
    }

    /** @test */
    public function teletravailleur_can_add_personal_task()
    {
        $this->actingAs($this->teletravailleur->utilisateur);

        $response = $this->postJson('/teletravailleur/tasks', [
            'title' => 'Réunion d’équipe',
            'description' => 'Préparer une présentation',
            'deadline' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Réunion d’équipe',
            'teletravailleur_id' => $this->teletravailleur->id,
        ]);
    }

    /** @test */
    public function teletravailleur_can_delete_personal_task()
    {
        $task = \App\Models\Calendar::create([
            'title' => 'Réunion d’équipe',
            'description' => 'Préparer une présentation',
            'deadline' => now()->addDays(3),
            'teletravailleur_id' => $this->teletravailleur->id,
            'created_by_admin' => false, // Tâche personnelle
        ]);

        $this->actingAs($this->teletravailleur->utilisateur);

        $response = $this->deleteJson("/teletravailleur/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function task_deadline_is_synced_with_hours_tracker()
    {
        $task = \App\Models\Calendar::create([
            'title' => 'Développer une fonctionnalité',
            'description' => 'Créer une nouvelle page',
            'deadline' => now()->addDays(5),
            'teletravailleur_id' => $this->teletravailleur->id,
            'created_by_admin' => true,
        ]);

        $this->actingAs($this->teletravailleur->utilisateur);

        // Simuler une requête pour vérifier le calendrier
        $response = $this->get('/teletravailleur/calendar');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Développer une fonctionnalité',
            'deadline' => $task->deadline->toDateString(),
        ]);
    }
}
