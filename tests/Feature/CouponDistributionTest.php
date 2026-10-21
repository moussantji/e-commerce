<?php

namespace Tests\Feature;

use App\Models\Commandes;
use App\Models\Paniers;
use App\Models\Produits;
use App\Models\PromoCode;
use App\Models\User;
use App\Notifications\CouponAssignedNotification;
use App\Services\CouponDistributionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use Tests\TestCase;

class CouponDistributionTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function automaticCoupon(array $attributes = []): PromoCode
    {
        return PromoCode::create(array_merge([
            'code' => 'AUTO-' . uniqid(),
            'type' => 'percentage',
            'value' => 2,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
            'usage_limit' => null,
            'usage_count' => 0,
            'is_active' => true,
            'is_automatic' => true,
        ], $attributes));
    }

    public function test_le_code_du_coupon_n_est_jamais_affiche_sur_l_accueil(): void
    {
        $coupon = $this->automaticCoupon(['code' => 'SECRET-GUGSAERTJ']);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee($coupon->code)
            ->assertDontSee('avec ' . $coupon->code);
    }

    public function test_l_api_ne_liste_que_les_coupons_attribues_au_client(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $coupon = $this->automaticCoupon();

        Sanctum::actingAs($user);

        $this->getJson('/api/coupons')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $coupon->users()->attach($user->id, [
            'sent_at' => now(),
            'source' => 'monthly',
        ]);

        $this->getJson('/api/coupons')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', $coupon->code);
    }

    public function test_les_seuils_de_commande_et_de_cumul_mensuel_sont_ceux_demandes(): void
    {
        $service = app(CouponDistributionService::class);

        $this->assertTrue($service->orderQualifies(100001, 1));
        $this->assertFalse($service->orderQualifies(100000, 1));
        $this->assertTrue($service->orderQualifies(1, 10));
        $this->assertFalse($service->orderQualifies(100000, 9));
    }

    public function test_un_cumul_mensuel_de_200_000_fcfa_attribue_et_envoie_le_coupon(): void
    {
        Notification::fake();

        $coupon = $this->automaticCoupon();
        $user = User::factory()->create(['role' => 'customer']);
        $product = Produits::factory()->create([
            'price' => 200000,
            'sale_price' => null,
            'stock' => 10,
        ]);

        $order = Commandes::create([
            'user_id' => $user->id,
            'paiement_id' => null,
            'livraison_id' => null,
            'numero_commande' => 'CMD-TEST-' . uniqid(),
            'statut' => 'en_attente',
            'sous_total' => 200000,
            'frais_livraison' => 0,
            'remise' => 0,
            'total' => 200000,
            'adresse_facturation' => [],
            'adresse_livraison' => [],
            'date_en_attente' => now(),
        ]);

        $order->produits()->attach($product->id, [
            'quantite' => 1,
            'prix_unitaire' => 200000,
            'total' => 200000,
        ]);

        $order->update(['statut' => 'payee']);

        Notification::assertSentTo($user, CouponAssignedNotification::class);
        $this->assertDatabaseHas('coupon_user', [
            'promo_code_id' => $coupon->id,
            'user_id' => $user->id,
            'source' => 'monthly',
        ]);
    }

    public function test_un_cumul_mensuel_de_21_articles_attribue_le_coupon(): void
    {
        Notification::fake();

        $coupon = $this->automaticCoupon();
        $user = User::factory()->create(['role' => 'customer']);
        $product = Produits::factory()->create([
            'price' => 1000,
            'sale_price' => null,
            'stock' => 50,
        ]);

        $order = Commandes::create([
            'user_id' => $user->id,
            'paiement_id' => null,
            'livraison_id' => null,
            'numero_commande' => 'CMD-TEST-' . uniqid(),
            'statut' => 'en_attente',
            'sous_total' => 21000,
            'frais_livraison' => 0,
            'remise' => 0,
            'total' => 21000,
            'adresse_facturation' => [],
            'adresse_livraison' => [],
            'date_en_attente' => now(),
        ]);

        $order->produits()->attach($product->id, [
            'quantite' => 21,
            'prix_unitaire' => 1000,
            'total' => 21000,
        ]);

        $order->update(['statut' => 'payee']);

        Notification::assertSentTo($user, CouponAssignedNotification::class);
        $this->assertDatabaseHas('coupon_user', [
            'promo_code_id' => $coupon->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_l_admin_cree_un_coupon_automatique_par_defaut(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('admin.coupons.index'))
            ->assertOk()
            ->assertSee('Aucun coupon automatique');

        $this->get(route('admin.coupons.create'))
            ->assertOk()
            ->assertSee('Distribution automatique');

        $this->post(route('admin.coupons.store'), [
            'code' => 'NOUVEAU-AUTO',
            'type' => 'percentage',
            'value' => 5,
            'usage_limit' => null,
            'starts_at' => now()->format('Y-m-d H:i:s'),
            'expires_at' => now()->addMonth()->format('Y-m-d H:i:s'),
            'is_active' => 1,
            'is_automatic' => 1,
        ])->assertRedirect(route('admin.coupons.index'));

        $coupon = PromoCode::where('code', 'NOUVEAU-AUTO')->firstOrFail();

        $this->assertTrue($coupon->is_automatic);
        $this->get(route('admin.coupons.edit', $coupon))
            ->assertOk()
            ->assertSee('Distribution automatique');
    }

    public function test_un_coupon_automatique_est_applique_au_panier_des_10_articles(): void
    {
        $coupon = $this->automaticCoupon();
        $user = User::factory()->create(['role' => 'customer']);
        $product = Produits::factory()->create([
            'price' => 200000,
            'sale_price' => null,
            'stock' => 20,
        ]);

        $panier = Paniers::create([
            'user_id' => $user->id,
            'status' => 'actif',
        ]);

        $panier->products()->attach($product->id, [
            'quantite' => 10,
            'prix_unitaire' => 200000,
            'total_ligne' => 2000000,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Cart::class)
            ->assertSet('itemsCount', 10)
            ->assertSet('autoCouponCode', $coupon->code)
            ->assertSet('discount', 40000.0);
    }

    public function test_une_commande_api_de_10_articles_applique_le_coupon_automatique(): void
    {
        Notification::fake();

        $coupon = $this->automaticCoupon();
        $user = User::factory()->create(['role' => 'customer']);
        $product = Produits::factory()->create([
            'price' => 200000,
            'sale_price' => null,
            'stock' => 20,
        ]);

        $panier = Paniers::create([
            'user_id' => $user->id,
            'status' => 'actif',
        ]);

        $panier->products()->attach($product->id, [
            'quantite' => 10,
            'prix_unitaire' => 200000,
            'total_ligne' => 2000000,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', [])->assertCreated();

        $order = Commandes::firstOrFail();
        $this->assertSame($coupon->id, $order->promo_code_id);
        $this->assertSame(40000.0, (float) $order->promo_discount);
        $this->assertSame(1960000.0, (float) $order->total);
        $this->assertSame(1960000.0, (float) $response->json('data.total'));

        $this->assertDatabaseHas('coupon_user', [
            'promo_code_id' => $coupon->id,
            'user_id' => $user->id,
            'source' => 'auto_order',
        ]);
    }

    public function test_le_coupon_mensuel_n_est_envoye_qu_une_fois_par_mois(): void
    {
        Carbon::setTestNow('2026-10-20 12:00:00');
        Notification::fake();

        $coupon = $this->automaticCoupon([
            'expires_at' => now()->addMonths(3),
        ]);
        $user = User::factory()->create(['role' => 'customer']);
        $service = app(CouponDistributionService::class);

        $this->assertTrue($service->send($user, $coupon, 'monthly'));
        $this->assertFalse($service->send($user, $coupon, 'monthly'));
        Notification::assertCount(1);

        $this->assertDatabaseHas('coupon_user', [
            'promo_code_id' => $coupon->id,
            'user_id' => $user->id,
            'last_sent_month' => '2026-10',
        ]);

        Carbon::setTestNow(now()->addMonth());

        $this->assertTrue($service->send($user, $coupon, 'monthly'));
        Notification::assertCount(2);

        Carbon::setTestNow();
    }

    public function test_un_coupon_automatique_non_attribue_est_refuse_par_l_api(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $coupon = $this->automaticCoupon();

        Sanctum::actingAs($user);

        $this->postJson('/api/coupons/apply', [
            'code' => $coupon->code,
            'subtotal' => 200000,
        ])->assertStatus(422);

        $coupon->users()->attach($user->id, [
            'sent_at' => now(),
            'source' => 'monthly',
        ]);

        $this->postJson('/api/coupons/apply', [
            'code' => $coupon->code,
            'subtotal' => 200000,
        ])
            ->assertOk()
            ->assertJsonPath('valid', true)
            ->assertJsonPath('discount', 4000);
    }
}
