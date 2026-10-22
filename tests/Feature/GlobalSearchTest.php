<?php

namespace Tests\Feature;

use App\Models\Produits;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_api_produits_permet_la_recherche_globale(): void
    {
        $produit = Produits::factory()->create([
            'name' => 'Climatiseur Zetyu',
            'price' => 125000,
            'stock' => 3,
            'is_active' => true,
        ]);

        $this->getJson('/api/products?search=zetyu&per_page=6')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $produit->id,
                'name' => 'Climatiseur Zetyu',
                ]);
    }

    public function test_la_recherche_est_disponible_sur_les_pages_compte(): void
    {
        $client = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        $this->actingAs($client)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="sm"', false)
            ->assertSee('id="shopSearchForm"', false);
    }

    public function test_la_recherche_est_disponible_sur_les_pages_sans_produits(): void
    {
        foreach ([route('aide'), route('conditions'), route('confidentialite')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('id="sm"', false)
                ->assertSee('id="sm-results"', false)
                ->assertSee('id="shopSearchForm"', false)
                ->assertSee('Produits populaires');
        }
    }
}
