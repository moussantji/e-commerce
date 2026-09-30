<?php

namespace Tests\Feature;

use App\Livewire\ProduitDetail;
use App\Models\Caracteristiques;
use App\Models\Produits as Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProduitDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_mount_resolves_product_and_options(): void
    {
        $carac = Caracteristiques::create(['name' => 'Capacité de stockage', 'type' => 'stockage', 'unite' => 'Go']);
        $product = Product::factory()->create();
        $product->caracteristiques()->attach($carac->id, ['value' => '256 Go']);

        $component = Livewire::test(ProduitDetail::class, ['product' => $product]);

        $component->assertSet('product.id', $product->id);
        // Groupes d'options calculés (stockage présent via la caractéristique liée)
        $this->assertNotEmpty($component->instance()->optionGroups());
        $this->assertSame('256 Go', $component->get('selectedOptions.stockage'));
    }

    public function test_quantity_increment_decrement_clamped(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        Livewire::test(ProduitDetail::class, ['product' => $product])
            ->assertSet('quantity', 1)
            ->call('incrementQty')
            ->assertSet('quantity', 2)
            ->call('incrementQty')
            ->assertSet('quantity', 3)
            ->call('incrementQty')
            ->assertSet('quantity', 3) // clampé au stock
            ->call('decrementQty')
            ->assertSet('quantity', 2)
            ->set('quantity', 99)
            ->assertSet('quantity', 3); // updatedQuantity clamp
    }

    public function test_add_to_cart_creates_panier_and_redirects(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['price' => 1000, 'sale_price' => 800, 'stock' => 5]);

        Livewire::actingAs($user)
            ->test(ProduitDetail::class, ['product' => $product])
            ->set('quantity', 2)
            ->call('addToCart', $product->id)
            ->assertRedirect(route('panier'));

        $this->assertDatabaseHas('panier_produit', [
            'produits_id' => $product->id,
            'quantite' => 2,
            'prix_unitaire' => 800, // prix soldé
        ]);
    }

    public function test_whatsapp_url_contains_selection(): void
    {
        $product = Product::factory()->create();

        $url = Livewire::test(ProduitDetail::class, ['product' => $product])
            ->instance()
            ->whatsappOrderUrl();

        $this->assertStringStartsWith('https://wa.me/', $url);
        $this->assertStringContainsString(rawurlencode($product->name), $url);
    }

    public function test_toggle_wishlist(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();

        $component = Livewire::actingAs($user)->test(ProduitDetail::class, ['product' => $product]);

        $this->assertFalse((bool) $component->get('iswishlisted'));
        $component->call('toggleWishlist', $product->id);
        $this->assertTrue((bool) $component->get('iswishlisted'));
        $this->assertDatabaseHas('wishlist_user_produit', [
            'user_id' => $user->id,
            'produits_id' => $product->id,
        ]);
    }

    public function test_similar_products_never_include_self(): void
    {
        $main = Product::factory()->create();
        Product::factory()->count(3)->create();

        $similar = Livewire::test(ProduitDetail::class, ['product' => $main])
            ->get('similarProducts');

        $this->assertNotEmpty($similar);
        foreach ($similar as $p) {
            $this->assertNotEquals($main->id, is_array($p) ? $p['id'] : $p->id);
        }
    }
}
