<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_teletravailleur_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'email' => $this->teletravailleurUser->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($this->teletravailleurUser);
        $response->assertRedirect('/teletravailleur/dashboard'); // Mise à jour de la redirection attendue
    }

    public function test_admin_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'email' => $this->adminUser->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertRedirect('/admin/dashboard'); // Mise à jour de la redirection attendue
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => $this->teletravailleurUser->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_teletravailleur_can_logout(): void
    {
        $response = $this->actingAs($this->teletravailleurUser)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login'); // Mise à jour de la redirection attendue
    }

    public function test_admin_can_logout(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login'); // Mise à jour de la redirection attendue
    }
}
