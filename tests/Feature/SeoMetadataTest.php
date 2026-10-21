<?php

namespace Tests\Feature;

use App\Models\Categories;
use App\Models\Produits;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_accueil_a_un_titre_une_description_et_un_seul_h1(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $response
            ->assertSee('<title>Boutique en ligne au Mali | ' . e(config('app.name')) . '</title>', false)
            ->assertSee('<link rel="canonical" href="' . url('/') . '">', false)
            ->assertSee('<meta name="description"', false);

        $this->assertSame(1, substr_count($response->getContent(), '<h1'));
    }

    public function test_page_categorie_a_un_h1_un_canonical_et_un_titre_propre(): void
    {
        $category = Categories::create([
            'name' => 'Électronique test',
            'slug' => 'electronique-test',
            'description' => 'Une sélection de produits électroniques testés.',
            'is_active' => true,
        ]);

        Produits::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $url = route('categories.show', $category->slug);

        $this->get($url)
            ->assertOk()
            ->assertSee('<h1>' . e($category->name) . '</h1>', false)
            ->assertSee('<link rel="canonical" href="' . $url . '">', false)
            ->assertSee('<meta name="robots" content="index, follow, max-image-preview:large">', false);
    }

    public function test_recherche_et_filtres_sont_en_noindex(): void
    {
        $this->get(route('products', ['q' => 'test']))
            ->assertOk()
            ->assertSee('<title>Recherche : « test » | ' . e(config('app.name')) . '</title>', false)
            ->assertSee('<meta name="robots" content="noindex, follow">', false);

        $this->get(route('products', ['promo' => 1]))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    public function test_titres_avec_apostrophe_ne_sont_pas_doublement_echappes(): void
    {
        $title = 'Conditions d&#039;utilisation | ' . e(config('app.name'));

        $this->get(route('conditions'))
            ->assertOk()
            ->assertSee('<title>' . $title . '</title>', false)
            ->assertDontSee('&amp;#039;', false)
            ->assertSee('<meta name="description" content="Conditions d&#039;utilisation de ' . e(config('app.name')), false);
    }

    public function test_page_404_est_en_noindex(): void
    {
        $this->get('/page-introuvable-' . uniqid())
            ->assertNotFound()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    public function test_sitemap_contient_les_pages_utiles_et_exclut_les_categories_vides(): void
    {
        $category = Categories::create([
            'name' => 'Catégorie avec produit',
            'slug' => 'categorie-avec-produit',
            'is_active' => true,
        ]);

        Produits::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $emptyCategory = Categories::create([
            'name' => 'Catégorie vide',
            'slug' => 'categorie-vide',
            'is_active' => true,
        ]);

        $this->get(route('categories.show', $emptyCategory->slug))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);

        $response = $this->get(route('sitemap'))->assertOk();

        $response
            ->assertSee(route('conditions'), false)
            ->assertSee(route('confidentialite'), false)
            ->assertSee(route('categories.show', $category->slug), false)
            ->assertDontSee(route('categories.show', $emptyCategory->slug), false);
    }
}
