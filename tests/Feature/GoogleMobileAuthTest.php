<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleMobileAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_code_oauth_mobile_est_echange_une_seule_fois_contre_un_token(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $code = Str::random(48);

        Cache::put('social-login:' . $code, $user->id, now()->addMinutes(5));

        $this->postJson('/api/auth/social/exchange', ['code' => $code])
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'mobile',
        ]);

        // Le code est à usage unique.
        $this->postJson('/api/auth/social/exchange', ['code' => $code])
            ->assertStatus(422);
    }

    public function test_un_code_oauth_mobile_invalide_est_refuse(): void
    {
        $this->postJson('/api/auth/social/exchange', [
            'code' => Str::random(48),
        ])->assertStatus(422);
    }

    public function test_le_callback_google_mobile_renvoie_un_code_a_l_app(): void
    {
        $socialUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $socialUser->shouldReceive('getEmail')->andReturn('google-mobile@example.com');
        $socialUser->shouldReceive('getId')->andReturn('google-123');
        $socialUser->shouldReceive('getName')->andReturn('Client Google');
        $socialUser->shouldReceive('getNickname')->andReturn('client');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this
            ->withSession([
                'social_mobile' => true,
                'social_return_to' => 'ecommerce://auth',
            ])
            ->get('/auth/google/callback');

        $response->assertRedirect();
        $location = $response->headers->get('Location');

        $this->assertStringStartsWith('ecommerce://auth?code=', $location);

        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);
        $this->assertNotEmpty($query['code'] ?? null);

        $this->postJson('/api/auth/social/exchange', ['code' => $query['code']])
            ->assertOk()
            ->assertJsonPath('user.email', 'google-mobile@example.com')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', [
            'email' => 'google-mobile@example.com',
            'provider' => 'google',
            'provider_id' => 'google-123',
        ]);
    }

    public function test_un_admin_connecte_via_google_mobile_revient_a_l_app_et_pas_au_dashboard_admin(): void
    {
        $admin = User::factory()->create([
            'email' => 'google-admin@example.com',
            'role' => 'admin',
        ]);

        $socialUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $socialUser->shouldReceive('getEmail')->andReturn('google-admin@example.com');
        $socialUser->shouldReceive('getId')->andReturn('google-admin-123');
        $socialUser->shouldReceive('getName')->andReturn('Admin Google');
        $socialUser->shouldReceive('getNickname')->andReturn('admin');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this
            ->withSession([
                'social_mobile' => true,
                'social_return_to' => 'ecommerce://auth',
            ])
            ->get('/auth/google/callback');

        $response->assertRedirect();
        $location = $response->headers->get('Location');

        // Le rôle ne doit jamais renvoyer un admin mobile vers le dashboard web.
        $this->assertStringStartsWith('ecommerce://auth?code=', $location);
        $this->assertStringNotContainsString('/admin/dashboard', $location);

        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);
        $this->assertNotEmpty($query['code'] ?? null);

        $this->postJson('/api/auth/social/exchange', ['code' => $query['code']])
            ->assertOk()
            ->assertJsonPath('user.id', $admin->id)
            ->assertJsonPath('user.role', 'admin')
            ->assertJsonStructure(['token']);
    }
}
