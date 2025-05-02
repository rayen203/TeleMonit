<?php

namespace Tests\Unit;

use Tests\TestCase;

class EnvironmentTest extends TestCase
{
    /** @test */
    public function environment_is_set_up_correctly()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function users_are_set_up_correctly()
    {
        $this->assertNotNull($this->teletravailleurUser);
        $this->assertNotNull($this->adminUser);
        $this->assertNotNull($this->teletravailleur);
        $this->assertNotNull($this->admin);
    }
}
