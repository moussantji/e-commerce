<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AccountStatusTest extends TestCase
{
    use RefreshDatabase;

    private function mockGoogleUser(string $email, string $id = 'google-status'): void
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

    public function test_un_client_banni_ne_peut_pas_se_connecter_sur_le_web(): void
    {
        User::factory()->create([
            'email' => 'banni@example.com',
            'role' => 'customer',
            'status' => 'inactive',
        ]);

        $response = $this->from('/')->post('/login', [
            'email' => 'banni@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'désactivé',
            (string) session('errors')->first('email'),
        );
    }

    public function test_un_vendeur_en_attente_ne_peut_pas_se_connecter_sur_le_web(): void
    {
        User::factory()->create([
            'email' => 'vendeur-attente@example.com',
            'role' => 'vendeur',
            'status' => 'inactive',
        ]);

        $response = $this->from('/')->post('/login', [
            'email' => 'vendeur-attente@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'attente de validation',
            (string) session('errors')->first('email'),
        );
    }

    public function test_un_client_actif_peut_se_connecter(): void
    {
        $user = User::factory()->create([
            'email' => 'actif@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->post('/login', [
            'email' => 'actif@example.com',
            'password' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_une_session_devenue_inactive_est_deconnectee(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user);

        $user->update(['status' => 'inactive']);

        $this->get('/dashboard')
            ->assertRedirect(route('home', ['connexion' => 1]))
            ->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_le_message_de_blocage_est_affiche_sur_l_accueil(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $message = $user->inactiveMessage();

        $user->update(['status' => 'inactive']);

        $this->actingAs($user)
            ->followingRedirects()
            ->get('/dashboard')
            ->assertOk()
            ->assertSee($message);
    }

    public function test_un_compte_inactif_ne_peut_pas_obtenir_de_token_api(): void
    {
        User::factory()->create([
            'email' => 'banni-api@example.com',
            'role' => 'customer',
            'status' => 'inactive',
        ]);

        $this->postJson('/api/login', [
            'email' => 'banni-api@example.com',
            'password' => 'password',
        ])
            ->assertStatus(403)
            ->assertJsonPath('code', 'account_inactive');
    }

    public function test_un_token_devenu_inactif_est_revoque(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $token = $user->createToken('mobile')->plainTextToken;

        $user->update(['status' => 'inactive']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertStatus(401)
            ->assertJsonPath('code', 'account_inactive');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_le_catalogue_reste_accessible_avec_un_token_inactif(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $token = $user->createToken('mobile')->plainTextToken;

        $user->update(['status' => 'inactive']);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/products')
            ->assertOk();
    }

    public function test_google_web_refuse_un_compte_inactif(): void
    {
        User::factory()->create([
            'email' => 'google-banni@example.com',
            'role' => 'customer',
            'status' => 'inactive',
        ]);

        $this->mockGoogleUser('google-banni@example.com', 'google-banni');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('home', ['connexion' => 1]))
            ->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_google_mobile_refuse_un_compte_inactif(): void
    {
        User::factory()->create([
            'email' => 'google-mobile-banni@example.com',
            'role' => 'customer',
            'status' => 'inactive',
        ]);

        $this->mockGoogleUser('google-mobile-banni@example.com', 'google-mobile-banni');

        $this->withSession([
            'social_mobile' => true,
            'social_return_to' => 'ecommerce://auth',
        ])->get('/auth/google/callback')
            ->assertRedirect('ecommerce://auth?error=inactive');

        $this->assertGuest();
    }

    public function test_l_api_social_refuse_un_compte_inactif(): void
    {
        Http::fake([
            'oauth2.googleapis.com/tokeninfo*' => Http::response([
                'aud' => config('services.google.client_id'),
                'email_verified' => 'true',
                'email' => 'google-api-banni@example.com',
                'sub' => 'google-api-banni',
                'name' => 'Banni API',
            ]),
        ]);

        User::factory()->create([
            'email' => 'google-api-banni@example.com',
            'role' => 'customer',
            'status' => 'inactive',
        ]);

        $this->postJson('/api/auth/social', [
            'provider' => 'google',
            'id_token' => 'jeton-test',
        ])
            ->assertStatus(403)
            ->assertJsonPath('code', 'account_inactive');
    }

    public function test_l_echange_du_code_oauth_refuse_un_compte_inactif(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'status' => 'inactive',
        ]);

        $code = Str::random(48);
        Cache::put('social-login:' . $code, $user->id, now()->addMinutes(5));

        $this->postJson('/api/auth/social/exchange', ['code' => $code])
            ->assertStatus(403)
            ->assertJsonPath('code', 'account_inactive');
    }
}
