<?php

namespace Tests\Feature;

use App\Models\Paniers;
use App\Models\photos as Photos;
use App\Models\Produits;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileOrderResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_articles_de_commande_ont_une_image_absolue_sans_erreur_request(): void
    {
        Notification::fake();

        // Force une URL de stockage relative, comme sur le serveur de production
        // quand Glide renvoie /images/... : c'est ce cas qui déclenchait
        // « Undefined variable $request » dans OrderResource.
        config(['filesystems.disks.public.url' => null]);

        $user = User::factory()->create(['role' => 'customer']);

        $product = Produits::factory()->create([
            'price' => 5000,
            'sale_price' => null,
            'stock' => 10,
        ]);

        Photos::create([
            'filename' => 'Produits/' . $product->id . '/photo.jpg',
            'produits_id' => $product->id,
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

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', [])->assertCreated();

        $image = $response->json('data.items.0.image');
        $orderId = $response->json('data.id');

        $this->assertIsString($image);
        $this->assertStringStartsWith('http', $image);
        $this->assertStringContainsString('Produits/', $image);

        // Le même Resource est utilisé par la liste et le détail : on vérifie
        // que $request n'est plus nécessaire dans la closure imbriquée.
        $this->getJson('/api/orders')
            ->assertOk()
            ->assertJsonPath('data.0.items.0.image', $image);

        $this->getJson("/api/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('data.items.0.image', $image);
    }
}
