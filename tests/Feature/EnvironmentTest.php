<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnvironmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function database_connection_is_working()
    {
        // Vérifier que la base de données est accessible
        $this->assertTrue(\DB::connection()->getPdo() instanceof \PDO);

        // Tester une requête simple (par exemple, vérifier que la table migrations existe)
        $migrationsTableExists = \Schema::hasTable('migrations');
        $this->assertTrue($migrationsTableExists, 'La table migrations devrait exister après la configuration de la base de données.');
    }
}
