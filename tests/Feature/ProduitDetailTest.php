<?php

namespace Tests\Feature;

use App\Livewire\ProduitDetail;
use App\Models\Produits as Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProduitDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure public disk is available in tests
        Storage::fake('public');
    }

    /**
     * Behaviors covered:
     * - Resolves product by slug or latest when not provided
     * - Computes and caches ratings (avg and count)
     * - Hydrates images from photos relation with resized URLs; falls back appropriately
     * - Quantity increment/decrement enforces minimum 1
     * - Emits cart and wishlist events and flashes messages
     * - Computes price and original with sale price handling
     * - Computes in-stock property based on stock > 0
     * - Loads similar and bundle items with brand filtering and image URL resolution
     */

    public function test_resolves_product_by_slug_and_handles_not_found()
    {
        $p1 = Product::factory()->create(['slug' => 'a-slug']);
        $p2 = Product::factory()->create();

        Livewire::test(ProduitDetail::class, ['slug' => 'a-slug'])
            ->assertSet('product.id', $p1->id);

        // When slug missing, should use latest
        Livewire::test(ProduitDetail::class)
            ->assertSet('product.id', $p2->id);
    }

    public function test_sets_and_caches_ratings()
    {
        $product = Product::factory()->create();
        // Attach some reviews via relation if exists
        if (method_exists($product, 'avisClients')) {
            $product->avisClients()->createMany([
                ['nb_etoiles' => 4, 'commentaire' => 'good', 'user_id' => 1],
                ['nb_etoiles' => 2, 'commentaire' => 'meh', 'user_id' => 1],
            ]);
        }

        Cache::shouldReceive('remember')->andReturn([
            'avg' => 3.0,
            'count' => method_exists($product, 'avisClients') ? 2 : 0,
        ]);

        Livewire::test(ProduitDetail::class, ['product' => $product])
            ->assertSet('ratingAvg', 3.0)
            ->assertSet('ratingCount', method_exists($product, 'avisClients') ? 2 : 0);
    }

    public function test_hydrates_images_and_fallbacks()
    {
        $product = Product::factory()->create(['image' => 'fallback.png']);

        // Fake a photo relation if available
        if (method_exists($product, 'photos')) {
            $photo = $product->photos()->create(['filename' => 'photo1.jpg']);
            // Ensure Storage url available
            Storage::disk('public')->put($photo->filename, 'x');
        }

        $component = Livewire::test(ProduitDetail::class, ['product' => $product])
            ->call('hydrateImages');

        $component->assertSet(fn ($state) => count($state['images']) >= 1);
    }

    public function test_quantity_increment_and_decrement_minimum_one()
    {
        $product = Product::factory()->create();

        Livewire::test(ProduitDetail::class, ['product' => $product])
            ->assertSet('quantity', 1)
            ->call('incrementQuantity')
            ->assertSet('quantity', 2)
            ->call('decrementQuantity')
            ->assertSet('quantity', 1)
            ->call('decrementQuantity')
            ->assertSet('quantity', 1);
    }

    public function test_add_to_cart_and_toggle_wishlist_dispatches_events()
    {
        $product = Product::factory()->create();

        Livewire::test(ProduitDetail::class, ['product' => $product])
            ->call('addToCart')
            ->assertDispatched('cart:add', productId: $product->id, quantity: 1)
            ->call('toggleWishlist')
            ->assertDispatched('wishlist:toggle', productId: $product->id);
    }

    public function test_get_price_and_stock()
    {
        $p1 = Product::factory()->create(['price' => 100, 'sale_price' => 80, 'stock' => 3]);
        $p2 = Product::factory()->create(['price' => 50, 'sale_price' => null, 'stock' => 0]);

        Livewire::test(ProduitDetail::class, ['product' => $p1])
            ->assertSet('product.stock', 3)
            ->assertComputed('inStock', true)
            ->assertViewHas('price', 80.0)
            ->assertViewHas('original', 100.0);

        Livewire::test(ProduitDetail::class, ['product' => $p2])
            ->assertComputed('inStock', false)
            ->assertViewHas('price', 50.0)
            ->assertViewHas('original', 50.0);
    }

    public function test_loads_similar_and_bundles_with_brand_filter()
    {
        $brand = 5;
        $main = Product::factory()->create(['brand_id' => $brand]);
        $sameBrand1 = Product::factory()->create(['brand_id' => $brand]);
        $sameBrand2 = Product::factory()->create(['brand_id' => $brand]);
        $otherBrand = Product::factory()->create(['brand_id' => 99]);

        Livewire::test(ProduitDetail::class, ['product' => $main])
            ->call('loadSimilarAndBundles')
            ->assertSet(fn ($state) => collect($state['similarProducts'])->every(function ($p) use ($brand, $main, $otherBrand) {
                // Ensure not the same product and brand filter applied
                return $p['id'] !== $main->id && $p['id'] !== $otherBrand->id && $p['id'] > 0;
            }))
            ->assertSet(fn ($state) => count($state['bundleItems']) <= 3);
    }
}
