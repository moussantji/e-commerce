<?php

namespace Tests\Feature;

use App\Models\Commandes;
use App\Models\Paiements;
use App\Models\Paniers;
use App\Models\PaymentProof;
use App\Models\Produits;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_commande_enregistre_le_moyen_de_paiement_choisi(): void
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'customer']);
        $method = $this->paymentMethod();
        $this->cartWithProduct($user);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', [
            'payment_method_id' => $method->id,
        ])->assertCreated();

        $orderId = $response->json('data.id');

        $response
            ->assertJsonPath('data.payment_method.id', $method->id)
            ->assertJsonPath('data.payment_method.name', 'Orange Money')
            ->assertJsonPath('data.payment_method.cod', false);

        $this->assertDatabaseHas('commandes', [
            'id' => $orderId,
            'paiement_id' => $method->id,
            'statut' => 'en_attente',
        ]);
    }

    public function test_la_preuve_de_paiement_avec_photo_declare_le_paiement(): void
    {
        Notification::fake();
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'customer']);
        $method = $this->paymentMethod();
        $orderId = $this->createOrder($user, $method->id);

        $file = UploadedFile::fake()->createWithContent(
            'preuve.png',
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='
            )
        );

        $response = $this->post(
            "/api/orders/{$orderId}/payment-proof",
            [
                'payment_method_id' => $method->id,
                'provider' => 'Orange Money',
                'phone' => '70000000',
                'transaction_id' => 'TX-123',
                'photos' => [$file],
            ],
            ['Accept' => 'application/json'],
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.statut', 'paiement_declare')
            ->assertJsonPath('data.payment_method.id', $method->id);

        $order = Commandes::findOrFail($orderId);
        $this->assertSame('paiement_declare', $order->statut);
        $this->assertSame($method->id, $order->paiement_id);

        $proof = PaymentProof::firstOrFail();
        $this->assertSame('pending', $proof->status);
        $this->assertCount(1, $proof->photos);
        Storage::disk('public')->assertExists($proof->photos[0]);

        // Le vendeur doit retrouver la capture dans son espace mobile.
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $this->getJson('/api/admin/payments')
            ->assertOk()
            ->assertJsonCount(1, 'data.0.photos');
    }

    public function test_un_paiement_mobile_money_sans_photo_est_refuse(): void
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'customer']);
        $method = $this->paymentMethod();
        $orderId = $this->createOrder($user, $method->id);

        $response = $this->postJson("/api/orders/{$orderId}/pay", [
            'payment_method_id' => $method->id,
            'provider' => 'Orange Money',
            'phone' => '70000000',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('code', 'payment_proof_required');

        $this->assertSame('en_attente', Commandes::findOrFail($orderId)->statut);
        $this->assertDatabaseCount('payment_proofs', 0);
    }

    private function createOrder(User $user, int $paymentMethodId): int
    {
        $this->cartWithProduct($user);
        Sanctum::actingAs($user);

        return $this->postJson('/api/orders', [
            'payment_method_id' => $paymentMethodId,
        ])->assertCreated()->json('data.id');
    }

    private function cartWithProduct(User $user): Produits
    {
        $product = Produits::factory()->create([
            'price' => 5000,
            'sale_price' => null,
            'stock' => 10,
        ]);

        $panier = Paniers::create([
            'user_id' => $user->id,
            'status' => 'actif',
        ]);

        $panier->products()->attach($product->id, [
            'quantite' => 1,
            'prix_unitaire' => 5000,
            'total_ligne' => 5000,
        ]);

        return $product;
    }

    private function paymentMethod(): Paiements
    {
        return Paiements::create([
            'method_name' => 'Orange Money',
            'provider_name' => 'orange',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
