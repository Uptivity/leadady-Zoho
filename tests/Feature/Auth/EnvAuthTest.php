<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class EnvAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('crm.username', 'admin');
        Config::set('crm.password', 'secret');
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get(route('crm.login.show'));

        $response->assertOk();
        $response->assertSee('Sign in');
    }

    public function test_user_is_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('crm.login.show'));
    }

    public function test_user_can_authenticate_with_valid_credentials(): void
    {
        $response = $this->post(route('crm.login.store'), [
            'username' => 'admin',
            'password' => 'secret',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('crm_authenticated', true);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->from(route('crm.login.show'))->post(route('crm.login.store'), [
            'username' => 'admin',
            'password' => 'wrong',
        ]);

        $response->assertRedirect(route('crm.login.show'));
        $response->assertSessionHasErrors('username');
    }

    public function test_logout_clears_session_and_redirects_to_login(): void
    {
        Session::start();

        $response = $this->withSession([
            'crm_authenticated' => true,
            '_token' => Session::token(),
        ])->post(route('crm.logout'), [
            '_token' => Session::token(),
        ]);

        $response->assertRedirect(route('crm.login.show'));
        $response->assertSessionMissing('crm_authenticated');
    }
}
