<?php

namespace Tests\Feature;

use App\Models\Commandes;
use App\Models\Livraison;
use App\Models\Paiements;
use App\Models\photos as Photos;
use App\Models\Produits;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SellerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_endpoints_vendeur_sont_reserves_aux_vendeurs(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        Sanctum::actingAs($customer);
        $this->getJson('/api/seller/dashboard')->assertForbidden();
        $this->getJson('/api/seller/products')->assertForbidden();
        $this->getJson('/api/seller/orders')->assertForbidden();
    }

    public function test_le_vendeur_ne_voit_que_ses_produits(): void
    {
        $seller = User::factory()->create(['role' => 'vendeur']);
        $other = User::factory()->create(['role' => 'vendeur']);

        $own = Produits::factory()->create([
            'vendeur_id' => $seller->id,
            'name' => 'Produit du vendeur',
        ]);
        Produits::factory()->create([
            'vendeur_id' => $other->id,
            'name' => 'Produit concurrent',
        ]);

        Sanctum::actingAs($seller);

        $this->getJson('/api/seller/products')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $own->id)
            ->assertJsonPath('data.0.name', 'Produit du vendeur');
    }

    public function test_le_vendeur_cree_un_produit_avec_photos_et_tags(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['role' => 'vendeur']);
        $tag = Tag::create(['name' => 'Nouveauté', 'slug' => 'nouveaute']);

        Sanctum::actingAs($seller);

        $response = $this->post(
            '/api/seller/products',
            [
                'name' => 'Sac artisanal',
                'description' => 'Un beau sac.',
                'price' => 15000,
                'sale_price' => 12000,
                'stock' => 4,
                'tags' => [$tag->id],
                'images' => [$this->fakePng('sac.png')],
                'is_active' => '1',
            ],
            ['Accept' => 'application/json'],
        );

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Sac artisanal')
            ->assertJsonPath('data.stock', 4)
            ->assertJsonPath('data.tags.0', $tag->id);

        $product = Produits::where('name', 'Sac artisanal')->firstOrFail();

        $this->assertSame($seller->id, $product->vendeur_id);
        $this->assertCount(1, $product->photos);
        $this->assertDatabaseHas('product_tag', [
            'produit_id' => $product->id,
            'tag_id' => $tag->id,
        ]);
        Storage::disk('public')->assertExists($product->photos->first()->filename);

        // En multipart, `tags_present` permet de vider tous les tags alors
        // qu'aucune clé `tags[]` n'est envoyée.
        $this->post(
            "/api/seller/products/{$product->id}",
            [
                '_method' => 'PUT',
                'name' => 'Sac artisanal',
                'price' => 15000,
                'stock' => 4,
                'tags_present' => '1',
                'images' => [$this->fakePng('second.png')],
            ],
            ['Accept' => 'application/json'],
        )->assertOk()->assertJsonPath('data.tags', []);

        $this->assertDatabaseMissing('product_tag', [
            'produit_id' => $product->id,
        ]);
        $this->assertCount(2, $product->fresh()->photos);
    }

    public function test_le_vendeur_ne_peut_pas_modifier_ou_supprimer_le_produit_d_un_autre(): void
    {
        $seller = User::factory()->create(['role' => 'vendeur']);
        $other = User::factory()->create(['role' => 'vendeur']);
        $otherProduct = Produits::factory()->create(['vendeur_id' => $other->id]);

        Sanctum::actingAs($seller);

        $this->putJson("/api/seller/products/{$otherProduct->id}", [
            'name' => 'Tentative',
            'price' => 100,
            'stock' => 1,
        ])->assertForbidden();

        $this->deleteJson("/api/seller/products/{$otherProduct->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('produits', ['id' => $otherProduct->id]);
    }

    public function test_le_tableau_de_bord_et_les_commandes_sont_limites_aux_produits_du_vendeur(): void
    {
        $seller = User::factory()->create(['role' => 'vendeur']);
        $other = User::factory()->create(['role' => 'vendeur']);
        $buyer = User::factory()->create(['role' => 'customer']);

        $ownProduct = Produits::factory()->create([
            'vendeur_id' => $seller->id,
            'price' => 5000,
            'stock' => 2,
        ]);
        $otherProduct = Produits::factory()->create([
            'vendeur_id' => $other->id,
            'price' => 9000,
            'stock' => 20,
        ]);

        // Une commande mixte : le vendeur ne doit voir que sa ligne.
        $mixedOrder = $this->orderFor($buyer, 'payee');
        $mixedOrder->produits()->attach($ownProduct->id, [
            'quantite' => 2,
            'prix_unitaire' => 5000,
            'total' => 10000,
        ]);
        $mixedOrder->produits()->attach($otherProduct->id, [
            'quantite' => 1,
            'prix_unitaire' => 9000,
            'total' => 9000,
        ]);

        // Une commande ne contenant que le produit de l'autre vendeur.
        $otherOrder = $this->orderFor($buyer, 'payee');
        $otherOrder->produits()->attach($otherProduct->id, [
            'quantite' => 1,
            'prix_unitaire' => 9000,
            'total' => 9000,
        ]);

        Sanctum::actingAs($seller);

        $this->getJson('/api/seller/dashboard')
            ->assertOk()
            ->assertJsonPath('stats.products', 1)
            ->assertJsonPath('stats.orders', 1)
            ->assertJsonPath('stats.revenue', 10000)
            ->assertJsonPath('stats.low_stock', 1)
            ->assertJsonCount(1, 'recent_orders')
            ->assertJsonPath('recent_orders.0.id', $mixedOrder->id)
            ->assertJsonPath('recent_orders.0.seller_total', 10000)
            ->assertJsonCount(1, 'recent_orders.0.items');

        $this->getJson('/api/seller/orders')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $mixedOrder->id)
            ->assertJsonPath('data.0.seller_total', 10000)
            ->assertJsonCount(1, 'data.0.items')
            ->assertJsonPath('data.0.items.0.product_id', $ownProduct->id);
    }

    public function test_le_vendeur_peut_supprimer_son_produit_et_ses_photos(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['role' => 'vendeur']);
        $product = Produits::factory()->create(['vendeur_id' => $seller->id]);
        Photos::create([
            'filename' => 'Produits/'.$product->id.'/photo.jpg',
            'produits_id' => $product->id,
        ]);
        Storage::disk('public')->put('Produits/'.$product->id.'/photo.jpg', 'image');

        Sanctum::actingAs($seller);

        $this->deleteJson("/api/seller/products/{$product->id}")
            ->assertOk();

        $this->assertDatabaseMissing('produits', ['id' => $product->id]);
        $this->assertDatabaseMissing('photos', ['produits_id' => $product->id]);
        Storage::disk('public')->assertMissing(
            'Produits/'.$product->id.'/photo.jpg',
        );
    }

    /** Petit PNG valide, sans dépendre de l'extension GD. */
    private function fakePng(string $name): UploadedFile
    {
        $content = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zl1sAAAAASUVORK5CYII=',
        );

        return UploadedFile::fake()->createWithContent($name, $content);
    }

    private function orderFor(User $buyer, string $status = 'en_attente'): Commandes
    {
        $payment = Paiements::create([
            'method_name' => 'Orange Money',
            'provider_name' => 'orange',
            'is_active' => true,
        ]);
        $shipping = Livraison::create([
            'method_name' => 'Standard',
            'price' => 0,
            'is_active' => true,
        ]);

        return Commandes::create([
            'user_id' => $buyer->id,
            'paiement_id' => $payment->id,
            'livraison_id' => $shipping->id,
            'numero_commande' => 'CMD-'.Str::upper(Str::random(10)),
            'statut' => $status,
            'sous_total' => 0,
            'frais_livraison' => 0,
            'remise' => 0,
            'total' => 0,
            'adresse_facturation' => ['nom' => 'Test'],
        ]);
    }
}
