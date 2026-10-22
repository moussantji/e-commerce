<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        // Le client est actif dès l'inscription (pas de validation admin).
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);
        // Le client est redirigé vers l'accueil après inscription.
        $response->assertRedirect(route('home', absolute: false));
    }
}
