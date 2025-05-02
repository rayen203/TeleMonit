<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function last_activity_is_updated_for_teletravailleur()
    {
        // Connecter le télétravailleur
        $this->actingAs($this->teletravailleurUser);

        // Mettre last_activity à une date antérieure de plus de 5 minutes
        $this->teletravailleurUser->last_activity = now()->subMinutes(6);
        $this->teletravailleurUser->save();

        // Simuler une action
        $originalLastActivity = $this->teletravailleurUser->last_activity;
        $this->get('/teletravailleur/dashboard');

        // Rafraîchir l'utilisateur depuis la base de données
        $this->teletravailleurUser->refresh();

        // Vérifier que last_activity a été mis à jour
        $this->assertNotEquals($originalLastActivity, $this->teletravailleurUser->last_activity);
        $this->assertTrue(Carbon::parse($this->teletravailleurUser->last_activity)->isToday());
    }

    /** @test */
    public function working_hours_are_recorded_for_teletravailleur()
    {
        // Connecter le télétravailleur
        $this->actingAs($this->teletravailleurUser);

        // Simuler le début d'une session de travail avec le champ action requis
        $response = $this->post('/teletravailleur/working-hours/start', [
            'action' => 'start', // Champ requis par la validation
        ]);

        $response->assertStatus(200);

        // Vérifier qu'une entrée a été créée dans working_hours
        $workingHour = WorkingHour::where('teletravailleur_id', $this->teletravailleur->id)
            ->whereDate('date', Carbon::today())
            ->first();

        $this->assertNotNull($workingHour);
        $this->assertNotNull($workingHour->start_time);
    }
}
