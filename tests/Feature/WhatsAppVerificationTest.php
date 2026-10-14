<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\WhatsAppVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.whatsapp.otp_driver' => 'log']);
    }

    public function test_profile_receives_whatsapp_code_and_verifies(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'tel' => '70000000', 'tel_verified_at' => null]);

        $this->actingAs($user)->post(route('profile.phone.send'))
            ->assertSessionHas('success');

        $code = Cache::get('wa_otp:22370000000');
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $code);

        $this->actingAs($user)->post(route('profile.phone.verify'), ['code' => $code])
            ->assertSessionHas('success');

        $this->assertNotNull($user->fresh()->tel_verified_at);
    }

    public function test_profile_rejects_wrong_whatsapp_code(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'tel' => '70000001', 'tel_verified_at' => null]);

        $this->actingAs($user)->post(route('profile.phone.send'))->assertSessionHas('success');

        $this->actingAs($user)->post(route('profile.phone.verify'), ['code' => '000000'])
            ->assertSessionHas('error');

        $this->assertNull($user->fresh()->tel_verified_at);
    }

    public function test_vendeur_is_created_directly_without_code(): void
    {
        // Création directe : infos => compte vendeur inactif, sans code.
        $this->post(route('vendeur.demande'), [
            'name' => 'Awa Diallo',
            'email' => 'awa@example.com',
            'tel' => '70000002',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHas('vendeur_created');

        // Aucun code OTP n'a été envoyé pour ce numéro.
        $this->assertNull(Cache::get('wa_otp:22370000002'));

        $vendeur = User::where('email', 'awa@example.com')->firstOrFail();
        $this->assertSame('vendeur', $vendeur->role);
        $this->assertSame('inactive', $vendeur->status);
        $this->assertSame('22370000002', $vendeur->tel);
        $this->assertNull($vendeur->tel_verified_at);
    }

    public function test_vendeur_code_route_is_an_alias_without_code(): void
    {
        // L'ancienne route /code crée aussi le compte directement, sans OTP.
        $this->post(route('vendeur.demande.code'), [
            'name' => 'Bakary Traoré',
            'email' => 'bakary@example.com',
            'tel' => '76000003',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHas('vendeur_created');

        $this->assertNull(Cache::get('wa_otp:22376000003'));
        $this->assertNotNull(User::where('email', 'bakary@example.com')->first());

        // Email déjà utilisé => erreur, pas de doublon.
        $this->post(route('vendeur.demande'), [
            'name' => 'Autre Nom',
            'email' => 'bakary@example.com',
            'tel' => '70000004',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email', null, 'vendeur');

        $this->assertSame(1, User::where('email', 'bakary@example.com')->count());
    }

    public function test_gateway_driver_sends_code_from_own_number(): void
    {
        config([
            'services.whatsapp.otp_driver' => 'gateway',
            'services.whatsapp.gateway.url' => 'https://gateway.test/send',
            'services.whatsapp.gateway.format' => 'form',
            'services.whatsapp.gateway.to_param' => 'to',
            'services.whatsapp.gateway.text_param' => 'body',
            'services.whatsapp.gateway.token' => 'tok123',
        ]);
        Http::fake(['https://gateway.test/*' => Http::response(['sent' => true], 200)]);

        $code = WhatsAppVerification::send('22370000009');

        Http::assertSent(fn ($req) => $req->url() === 'https://gateway.test/send'
            && $req['to'] === '22370000009'
            && $req['token'] === 'tok123'
            && str_contains((string) $req['body'], (string) $code));

        $this->assertTrue(WhatsAppVerification::check('22370000009', $code));
    }

    public function test_gateway_failure_throws(): void
    {
        config([
            'services.whatsapp.otp_driver' => 'gateway',
            'services.whatsapp.gateway.url' => 'https://gateway.test/send',
        ]);
        Http::fake(['*' => Http::response('ko', 500)]);

        $this->expectException(\RuntimeException::class);

        WhatsAppVerification::send('22370000009');
    }

    public function test_malian_prefixes_82_and_64_are_accepted(): void
    {
        // Plages réelles : 82... (business), 64... (opérateurs), 20... (fixes).
        $this->assertSame('22382019583', \App\Support\PhoneNumber::normalize('82019583'));
        $this->assertSame('22364356060', \App\Support\PhoneNumber::normalize('64356060'));
        $this->assertSame('22320000000', \App\Support\PhoneNumber::normalize('20000000'));
        $this->assertNull(\App\Support\PhoneNumber::normalize('1234567'));

        // Parcours vendeur direct avec un numéro en 82 (sans code).
        $this->post(route('vendeur.demande'), [
            'name' => 'Fanta Diarra',
            'email' => 'fanta@example.com',
            'tel' => '82019583',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHas('vendeur_created');

        $this->assertSame('22382019583', User::where('email', 'fanta@example.com')->firstOrFail()->tel);
    }
}
