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

    public function test_vendeur_two_steps_creates_verified_inactive_account(): void
    {
        // Étape 1 : infos => code envoyé, compte NON créé.
        $this->post(route('vendeur.demande.code'), [
            'name' => 'Awa Diallo',
            'email' => 'awa@example.com',
            'tel' => '70000002',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHas('vendeur_code_sent');

        $this->assertDatabaseMissing('users', ['email' => 'awa@example.com']);

        $code = Cache::get('wa_otp:22370000002');
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $code);

        // Mauvais code => pas de compte.
        $this->post(route('vendeur.demande'), ['tel' => '70000002', 'code' => '000000'])
            ->assertSessionHasErrors('code', null, 'vendeur');
        $this->assertDatabaseMissing('users', ['email' => 'awa@example.com']);

        // Bon code => compte vendeur inactif, numéro vérifié.
        $this->post(route('vendeur.demande'), ['tel' => '70000002', 'code' => $code])
            ->assertSessionHas('vendeur_created');

        $vendeur = User::where('email', 'awa@example.com')->firstOrFail();
        $this->assertSame('vendeur', $vendeur->role);
        $this->assertSame('inactive', $vendeur->status);
        $this->assertSame('22370000002', $vendeur->tel);
        $this->assertNotNull($vendeur->tel_verified_at);
    }

    public function test_vendeur_code_can_be_resent(): void
    {
        $this->post(route('vendeur.demande.code'), [
            'name' => 'Bakary Traoré',
            'email' => 'bakary@example.com',
            'tel' => '76000003',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHas('vendeur_code_sent');

        $first = Cache::get('wa_otp:22376000003');

        $this->post(route('vendeur.demande.code'), ['tel' => '76000003'])
            ->assertSessionHas('vendeur_code_sent');

        // La demande en attente est conservée (création toujours possible).
        $this->assertNotNull(WhatsAppVerification::peekPending('22376000003'));
        $this->assertNotEmpty(Cache::get('wa_otp:22376000003'));
        $this->assertNotSame($first, null);
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
}
