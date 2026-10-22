<?php

namespace Tests\Feature;

use App\Livewire\Admin\AddProduct;
use App\Livewire\Admin\EditProduct;
use App\Models\Categories;
use App\Models\Produits;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use Tests\TestCase;

class JsonImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private const PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zl1sAAAAASUVORK5CYII=';

    public function test_un_vendeur_peut_televerser_une_image_en_json(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['role' => 'vendeur']);
        Sanctum::actingAs($seller);

        $response = $this->postJson('/api/uploads/images', [
            'name' => 'photo.png',
            'mime' => 'image/png',
            'data' => self::PNG_BASE64,
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['path', 'url']);

        $path = $response->json('path');

        $this->assertStringStartsWith('uploads/vendeur-'.$seller->id.'/', $path);
        Storage::disk('public')->assertExists($path);

        // L'URL d'aperçu passe par Glide, comme les photos définitives.
        $url = $response->json('url');
        $this->assertStringContainsString('/images/', $url);
        $this->assertStringContainsString($path, $url);
    }

    public function test_un_client_ne_peut_pas_televerser_d_image(): void
    {
        Storage::fake('public');

        $customer = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customer);

        $this->postJson('/api/uploads/images', [
            'name' => 'photo.png',
            'mime' => 'image/png',
            'data' => self::PNG_BASE64,
        ])->assertForbidden();
    }

    public function test_le_televersement_refuse_une_image_invalide(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['role' => 'vendeur']);
        Sanctum::actingAs($seller);

        $this->postJson('/api/uploads/images', [
            'data' => 'ceci-n-est-pas-une-image',
        ])->assertStatus(422)->assertJsonValidationErrors('data');
    }

    public function test_le_vendeur_api_attache_une_image_televersee_a_son_produit(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['role' => 'vendeur']);
        Sanctum::actingAs($seller);

        $path = $this->uploadFor($seller);

        $response = $this->postJson('/api/seller/products', [
            'name' => 'Produit avec image JSON',
            'description' => 'Test',
            'price' => 15000,
            'stock' => 3,
            'uploaded_images' => [$path],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Produit avec image JSON');

        $product = Produits::where('name', 'Produit avec image JSON')->firstOrFail();

        $this->assertSame($seller->id, $product->vendeur_id);
        $this->assertCount(1, $product->photos);
        $this->assertProductImage($product, $path);
    }

    public function test_un_vendeur_ne_peut_pas_attacher_l_image_d_un_autre_vendeur(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['role' => 'vendeur']);
        $other = User::factory()->create(['role' => 'vendeur']);

        $foreignPath = 'uploads/vendeur-'.$other->id.'/foreign.png';
        Storage::disk('public')->put($foreignPath, base64_decode(self::PNG_BASE64));

        Sanctum::actingAs($seller);

        $this->postJson('/api/seller/products', [
            'name' => 'Tentative image étrangère',
            'price' => 1000,
            'stock' => 1,
            'uploaded_images' => [$foreignPath],
        ])->assertCreated();

        $product = Produits::where('name', 'Tentative image étrangère')->firstOrFail();

        $this->assertCount(0, $product->photos);
        $this->assertNull($product->image);
    }

    public function test_l_admin_mobile_attache_une_image_json_a_un_produit(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $path = $this->uploadFor($admin);

        $this->postJson('/api/admin/manage/products', [
            'name' => 'Produit admin JSON',
            'description' => '',
            'price' => 2000,
            'stock' => 2,
            'uploaded_images' => [$path],
        ])->assertCreated();

        $product = Produits::where('name', 'Produit admin JSON')->firstOrFail();

        $this->assertCount(1, $product->photos);
        $this->assertProductImage($product, $path);
    }

    public function test_l_admin_mobile_attache_une_image_unique_json_a_une_categorie(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $path = $this->uploadFor($admin);

        $this->postJson('/api/admin/manage/categories', [
            'name' => 'Catégorie JSON',
            'description' => '',
            'uploaded_image' => $path,
        ])->assertCreated();

        $category = Categories::where('name', 'Catégorie JSON')->firstOrFail();

        $this->assertCount(1, $category->photos);
        $this->assertStringStartsWith('Category/'.$category->id.'/', (string) $category->image);
        $this->assertSame($category->image, $category->photos->first()->filename);
        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    public function test_le_vendeur_web_attache_une_image_json_a_son_produit(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['role' => 'vendeur']);
        $this->actingAs($seller);

        $path = $this->uploadFor($seller);

        $this->post('/vendeur/produits', [
            'name' => 'Produit vendeur web JSON',
            'description' => 'Test',
            'price' => 12000,
            'stock' => 2,
            'uploaded_images' => [$path],
            'is_active' => '1',
        ])->assertRedirect(route('vendeur.products.index'));

        $product = Produits::where('name', 'Produit vendeur web JSON')->firstOrFail();

        $this->assertCount(1, $product->photos);
        $this->assertProductImage($product, $path);
    }

    public function test_le_vendeur_web_peut_creer_un_produit_sans_description(): void
    {
        Storage::fake('public');

        $seller = User::factory()->create(['role' => 'vendeur']);
        $this->actingAs($seller);

        $path = $this->uploadFor($seller);

        // La colonne `description` est NOT NULL en base : le formulaire la
        // laisse vide, l'enregistrement ne doit pas planter.
        $this->post('/vendeur/produits', [
            'name' => 'Produit vendeur sans description',
            'price' => 9000,
            'stock' => 1,
            'uploaded_images' => [$path],
            'is_active' => '1',
        ])->assertRedirect(route('vendeur.products.index'));

        $product = Produits::where('name', 'Produit vendeur sans description')->firstOrFail();

        $this->assertSame('', $product->description);
        $this->assertCount(1, $product->photos);
        $this->assertProductImage($product, $path);
    }

    public function test_l_admin_web_ajoute_une_image_json_depuis_livewire(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        Livewire::actingAs($admin);

        $path = $this->uploadFor($admin);

        Livewire::test(AddProduct::class)
            ->set('name', 'Produit Livewire JSON')
            ->set('description', 'Test')
            ->set('regular_price', 5000)
            ->set('stock_quantity', 4)
            ->call('addUploadedImages', [$path])
            ->call('submit')
            ->assertHasNoErrors();

        $product = Produits::where('name', 'Produit Livewire JSON')->firstOrFail();

        $this->assertCount(1, $product->photos);
        $this->assertProductImage($product, $path);
    }

    public function test_l_admin_web_modifie_un_produit_avec_une_image_json(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        Livewire::actingAs($admin);

        $product = Produits::factory()->create([
            'name' => 'Produit à modifier',
            'price' => 5000,
        ]);
        $path = $this->uploadFor($admin);

        Livewire::test(EditProduct::class, ['produit_id' => $product->id])
            ->call('addUploadedImages', [$path])
            ->call('update')
            ->assertHasNoErrors();

        $product->refresh();

        $this->assertCount(1, $product->photos);
        $this->assertProductImage($product, $path);
    }

    public function test_l_admin_web_ajoute_un_produit_sans_description(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        Livewire::actingAs($admin);

        $path = $this->uploadFor($admin);

        Livewire::test(AddProduct::class)
            ->set('name', 'Produit Livewire sans description')
            ->set('regular_price', 7500)
            ->set('stock_quantity', 2)
            ->call('addUploadedImages', [$path])
            ->call('submit')
            ->assertHasNoErrors();

        $product = Produits::where('name', 'Produit Livewire sans description')->firstOrFail();

        $this->assertSame('', $product->description);
        $this->assertCount(1, $product->photos);
        $this->assertProductImage($product, $path);
    }

    /**
     * L'image attachée doit être déplacée dans Produits/{id}/ (même
     * convention que Produits::attachfiles()) : le fichier temporaire
     * uploads/{role}-{id}/... ne doit plus exister.
     */
    private function assertProductImage(Produits $product, string $tempPath): void
    {
        $stored = (string) $product->image;

        $this->assertStringStartsWith('Produits/'.$product->id.'/', $stored);
        $this->assertSame($stored, $product->photos->first()->filename);
        $this->assertTrue(Storage::disk('public')->exists($stored));
        $this->assertFalse(
            Storage::disk('public')->exists($tempPath),
            'Le fichier temporaire aurait dû être déplacé.'
        );
    }

    private function uploadFor(User $user): string
    {
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/uploads/images', [
            'name' => 'photo.png',
            'mime' => 'image/png',
            'data' => self::PNG_BASE64,
        ])->assertCreated();

        return $response->json('path');
    }
}
