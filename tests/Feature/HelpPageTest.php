<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_aide_est_accessible(): void
    {
        $this->get(route('aide'))
            ->assertOk()
            ->assertSee('Comment pouvons-nous vous aider ?')
            ->assertSee('Questions fréquentes')
            ->assertSee('contact@mandenbaoubab.com');
    }

    public function test_lien_aide_du_header_pointe_vers_la_page_aide(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="' . route('aide') . '"', false)
            ->assertDontSee('href="' . route('home') . '#aide"', false);
    }
}
