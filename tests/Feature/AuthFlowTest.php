<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * US-01: verifica el login mock, la protección de rutas por middleware
 * y el flujo de logout / landmark. No toca la BD (el middleware de auth
 * corre antes que cualquier consulta al controlador).
 */
class AuthFlowTest extends TestCase
{
    public function test_dashboard_redirects_to_login_when_unauthenticated(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_root_redirects_to_login_when_unauthenticated(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));
    }

    public function test_login_page_is_public_and_renders_providers(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Ingresar con Google')
            ->assertSee('Ingresar con Microsoft');
    }

    public function test_post_login_sets_session_and_redirects_to_dashboard(): void
    {
        $this->post(route('login.post'), ['nombre' => 'Juan Pérez', 'proveedor' => 'google', '_token' => csrf_token()])
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(session('logged_in'));
        $this->assertEquals('Juan Pérez', session('nombre'));
        $this->assertEquals('google', session('proveedor'));
    }

    public function test_root_redirects_to_dashboard_when_authenticated(): void
    {
        $this->withSession(['logged_in' => true])->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_login_redirects_to_dashboard_when_already_authenticated(): void
    {
        $this->withSession(['logged_in' => true])->get(route('login'))->assertRedirect(route('dashboard'));
    }

    public function test_logout_destroys_session_and_redirects_to_login(): void
    {
        $this->withSession(['logged_in' => true])
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        // logout() hace forget('logged_in'): la clave queda null (se removió).
        $this->assertNull(session('logged_in'));
        $this->assertNull(session('proveedor'));
    }

    public function test_proyectos_index_redirects_to_login_when_unauthenticated(): void
    {
        $this->get(route('proyectos.index'))->assertRedirect(route('login'));
    }
}
