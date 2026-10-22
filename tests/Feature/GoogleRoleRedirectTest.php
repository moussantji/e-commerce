<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleRoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    /** Simule le retour de Google pour un utilisateur donné. */
    private function mockGoogleUser(string $email, string $id = 'google-123'): void
    {
        $socialUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $socialUser->shouldReceive('getEmail')->andReturn($email);
        $socialUser->shouldReceive('getId')->andReturn($id);
        $socialUser->shouldReceive('getName')->andReturn('Utilisateur Google');
        $socialUser->shouldReceive('getNickname')->andReturn('utilisateur');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);
    }

    public function test_un_admin_google_est_redirige_vers_le_tableau_de_bord_admin(): void
    {
        User::factory()->create([
            'email' => 'admin-google@example.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->mockGoogleUser('admin-google@example.com', 'google-admin');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_un_vendeur_google_est_redirige_vers_son_tableau_de_bord(): void
    {
        User::factory()->create([
            'email' => 'vendeur-google@example.com',
            'role' => 'vendeur',
            'status' => 'active',
        ]);

        $this->mockGoogleUser('vendeur-google@example.com', 'google-vendeur');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('vendeur.dashboard'));
    }

    public function test_un_client_google_est_redirige_vers_l_accueil(): void
    {
        User::factory()->create([
            'email' => 'client-google@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->mockGoogleUser('client-google@example.com', 'google-client');

        $this->get('/auth/google/callback')
            ->assertRedirect('/');
    }

    public function test_un_nouveau_compte_google_devient_un_client_actif(): void
    {
        $this->mockGoogleUser('nouveau-google@example.com', 'google-nouveau');

        $this->get('/auth/google/callback')->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'nouveau-google@example.com',
            'role' => 'customer',
            'status' => 'active',
            'provider' => 'google',
            'provider_id' => 'google-nouveau',
        ]);
    }
}
