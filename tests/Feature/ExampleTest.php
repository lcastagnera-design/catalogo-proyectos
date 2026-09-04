<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La raíz redirige al login porque la app exige autenticación (US-01).
     */
    public function test_the_application_redirects_unauthenticated_users_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }
}
