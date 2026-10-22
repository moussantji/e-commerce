<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_conditions_est_accessible_et_structuree(): void
    {
        $this->get(route('conditions'))
            ->assertOk()
            ->assertSee("Conditions d'utilisation")
            ->assertSee('Dernière mise à jour')
            ->assertSee('22 octobre 2026')
            ->assertSee('Sommaire')
            ->assertSee('href="#objet"', false)
            ->assertSee('href="#commandes-paiements"', false)
            ->assertSee('id="objet"', false)
            ->assertSee('id="contact"', false)
            ->assertSee('Les paiements s\'effectuent via les moyens Mobile Money proposés', false)
            ->assertSee('class="shop-topbar"', false)
            ->assertSee('class="shop-footer"', false)
            ->assertSee('Politique de confidentialité');
    }

    public function test_la_page_confidentialite_est_accessible_et_structuree(): void
    {
        $this->get(route('confidentialite'))
            ->assertOk()
            ->assertSee('Politique de confidentialité')
            ->assertSee('Dernière mise à jour')
            ->assertSee('22 octobre 2026')
            ->assertSee('Sommaire')
            ->assertSee('href="#donnees-collectees"', false)
            ->assertSee('href="#droits"', false)
            ->assertSee('id="donnees-collectees"', false)
            ->assertSee('id="contact"', false)
            ->assertSee('Vos données ne sont pas vendues.', false)
            ->assertSee('class="shop-topbar"', false)
            ->assertSee('class="shop-footer"', false)
            ->assertSee("Conditions d'utilisation");
    }
}
