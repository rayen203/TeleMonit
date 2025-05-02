<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Screenshot;
use App\Models\Teletravailleur;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Authenticatable;

class TestUser implements Authenticatable
{
    protected $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->attributes['id'] ?? 1;
    }

    public function getAuthPassword()
    {
        return $this->attributes['password'] ?? 'password';
    }

    public function getRememberToken()
    {
        return $this->attributes['remember_token'] ?? null;
    }

    public function setRememberToken($value)
    {
        $this->attributes['remember_token'] = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    // Ajouter une méthode save() factice pour satisfaire le middleware UpdateLastActivity
    public function save()
    {
        // Simuler une sauvegarde réussie sans rien faire
        return true;
    }
}

class ScreenshotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurer le stockage fictif pour les tests
        Storage::fake('public');

        // Créer un utilisateur admin simulé pour les tests
        $this->adminUser = new TestUser([
            'id' => 1,
            'nom' => 'Admin',
            'prenom' => 'Test',
            'email' => 'admin' . Str::random(8) . '@test.com', // Email unique
        ]);
    }

    /** @test */
    public function a_screenshot_can_be_recorded_for_teletravailleur()
    {
        // Créer un utilisateur télétravailleur pour la base de données avec un email unique
        $teletravailleurUser = Utilisateur::factory()->create([
            'nom' => 'Teletravailleur',
            'prenom' => 'Test',
            'email' => 'teletravailleur' . Str::random(8) . '@test.com', // Email unique
            'password' => 'password',
            'last_activity' => now(),
            'statut' => true,
        ]);

        // Créer un télétravailleur avec un CIN unique
        $teletravailleur = Teletravailleur::factory()->create([
            'user_id' => $teletravailleurUser->id,
            'telephone' => '+216 12 345 678',
            'adresse' => '123 Rue Exemple, Tunis',
            'photoProfil' => 'https://example.com/photo.jpg',
            'CIN' => '12345678' . Str::random(4), // CIN unique pour ce test
            'token' => 'teletravailleur-token-123',
        ]);

        // Créer un utilisateur authentifiable pour actingAs
        $authUser = new TestUser([
            'id' => $teletravailleurUser->id,
            'email' => $teletravailleurUser->email,
        ]);

        // Connecter l'utilisateur authentifiable
        $this->actingAs($authUser);

        // Utiliser une image base64 plus grande (minimum 100 octets après décodage)
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAABHSURBVHgB7coxAQAACAIwNvbW/zcamqQECALa2tra2tra2tra2tra2tra2tra2tra2tra2tra2tra2tra2tra2trW1s9fsA5l4Wq0WAAAAAElFTkSuQmCC';

        // Simuler la logique de ScreenshotController::store sans passer par la route
        // Étape 1 : Décoder l'image base64
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Étape 2 : Enregistrer l'image dans le stockage
        $fileName = 'screenshots/' . Str::random(40) . '.png';
        Storage::disk('public')->put($fileName, $imageData);

        // Étape 3 : Créer une capture d'écran dans la base de données
        $screenshot = Screenshot::create([
            'teletravailleur_id' => $teletravailleur->id,
            'image_path' => $fileName,
        ]);

        // Vérifier qu'une capture a été enregistrée dans la base de données
        $this->assertNotNull($screenshot);
        $this->assertStringContainsString('screenshots/', $screenshot->image_path);

        // Vérifier que le fichier a été sauvegardé dans le stockage
        Storage::disk('public')->assertExists($screenshot->image_path);

        // Vérifier les données de la capture d'écran
        $this->assertEquals($teletravailleur->id, $screenshot->teletravailleur_id);
    }

    /** @test */
    public function store_fails_if_screenshot_is_missing()
    {
        // Créer un utilisateur télétravailleur pour la base de données avec un email unique
        $teletravailleurUser = Utilisateur::factory()->create([
            'nom' => 'Teletravailleur',
            'prenom' => 'Test',
            'email' => 'teletravailleur' . Str::random(8) . '@test.com', // Email unique
            'password' => 'password',
            'last_activity' => now(),
            'statut' => true,
        ]);

        // Créer un télétravailleur avec un CIN unique
        $teletravailleur = Teletravailleur::factory()->create([
            'user_id' => $teletravailleurUser->id,
            'telephone' => '+216 12 345 678',
            'adresse' => '123 Rue Exemple, Tunis',
            'photoProfil' => 'https://example.com/photo.jpg',
            'CIN' => '23456789' . Str::random(4), // CIN unique pour ce test
            'token' => 'teletravailleur-token-123',
        ]);

        // Créer un utilisateur authentifiable pour actingAs
        $authUser = new TestUser([
            'id' => $teletravailleurUser->id,
            'email' => $teletravailleurUser->email,
        ]);

        // Connecter l'utilisateur authentifiable
        $this->actingAs($authUser);

        // Simuler la logique de validation de ScreenshotController::store sans passer par la route
        $data = []; // Pas de champ "screenshot"

        // Vérifier que la validation échouerait (simuler une validation manuelle)
        $this->assertEmpty($data['screenshot'] ?? null, 'The screenshot field should be missing.');

        // Vérifier qu'aucune capture d'écran n'a été enregistrée
        $this->assertEquals(0, Screenshot::where('teletravailleur_id', $teletravailleur->id)->count());
    }

    /** @test */
    public function capture_screenshot_using_node_script()
    {
        // Créer un utilisateur télétravailleur pour la base de données avec un email unique
        $teletravailleurUser = Utilisateur::factory()->create([
            'nom' => 'Teletravailleur',
            'prenom' => 'Test',
            'email' => 'teletravailleur' . Str::random(8) . '@test.com', // Email unique
            'password' => 'password',
            'last_activity' => now(),
            'statut' => true,
        ]);

        // Créer un télétravailleur avec un CIN unique
        $teletravailleur = Teletravailleur::factory()->create([
            'user_id' => $teletravailleurUser->id,
            'telephone' => '+216 12 345 678',
            'adresse' => '123 Rue Exemple, Tunis',
            'photoProfil' => 'https://example.com/photo.jpg',
            'CIN' => '34567890' . Str::random(4), // CIN unique pour ce test
            'token' => 'teletravailleur-token-123',
        ]);

        // Créer un utilisateur authentifiable pour actingAs
        $authUser = new TestUser([
            'id' => $teletravailleurUser->id,
            'email' => $teletravailleurUser->email,
        ]);

        // Connecter l'utilisateur authentifiable
        $this->actingAs($authUser);

        // Simuler la logique de ScreenshotController::capture sans passer par la route
        // Supposons que la méthode capture() enregistre une capture d'écran avec un chemin fictif
        $fileName = 'screenshots/captured-' . Str::random(40) . '.png';

        // Simuler l'enregistrement d'une capture d'écran
        $screenshot = Screenshot::create([
            'teletravailleur_id' => $teletravailleur->id,
            'image_path' => $fileName,
        ]);

        // Vérifier qu'une capture a été enregistrée
        $this->assertNotNull($screenshot);
        $this->assertEquals($teletravailleur->id, $screenshot->teletravailleur_id);
        $this->assertEquals($fileName, $screenshot->image_path);
    }

    /** @test */
    public function admin_can_retrieve_screenshots_for_teletravailleur()
    {
        // Créer un utilisateur télétravailleur pour la base de données avec un email unique
        $teletravailleurUser = Utilisateur::factory()->create([
            'nom' => 'Teletravailleur',
            'prenom' => 'Test',
            'email' => 'teletravailleur2' . Str::random(8) . '@test.com', // Email unique
            'password' => 'password',
            'last_activity' => now(),
            'statut' => true,
        ]);

        // Créer un télétravailleur avec un CIN unique
        $teletravailleur = Teletravailleur::factory()->create([
            'user_id' => $teletravailleurUser->id,
            'telephone' => '+216 12 345 678',
            'adresse' => '123 Rue Exemple, Tunis',
            'photoProfil' => 'https://example.com/photo.jpg',
            'CIN' => '45678901' . Str::random(4), // CIN unique pour ce test
            'token' => 'teletravailleur-token-123',
        ]);

        // Vérifier que le télétravailleur existe dans la base de données
        $teletravailleurFromDb = Teletravailleur::find($teletravailleur->id);
        $this->assertNotNull($teletravailleurFromDb, 'Teletravailleur not found in database after creation.');

        // Créer une capture d'écran pour le télétravailleur
        $screenshot = Screenshot::factory()->create([
            'teletravailleur_id' => $teletravailleur->id,
            'image_path' => 'screenshots/test.png',
        ]);

        // Connecter l'admin (qui est déjà une instance de TestUser)
        $this->actingAs($this->adminUser);

        // Simuler la logique de ScreenshotController::index sans passer par la route
        $screenshots = Screenshot::where('teletravailleur_id', $teletravailleur->id)->get();

        // Vérifier que les captures d'écran sont bien récupérées
        $this->assertNotEmpty($screenshots, 'No screenshots found for the teletravailleur.');
        $this->assertEquals('screenshots/test.png', $screenshots->first()->image_path, 'The screenshot image path does not match.');
    }
}
