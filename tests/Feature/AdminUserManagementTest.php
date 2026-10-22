<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_formulaire_de_modification_affiche_le_bon_utilisateur(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Connecté',
            'email' => 'admin-connecte@example.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $target = User::factory()->create([
            'name' => 'Client Cible',
            'email' => 'client-cible@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.edit', $target));

        $response->assertOk();
        $response->assertSee('Client Cible');
        $response->assertSee('client-cible@example.com');
        $response->assertSee('action="' . route('admin.users.update', $target) . '"', false);
        $response->assertDontSee('action="' . route('admin.users.update', $admin) . '"', false);
        $response->assertDontSee('value="Admin Connecté"', false);
    }

    public function test_la_modification_enregistre_le_bon_utilisateur(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Connecté',
            'email' => 'admin-connecte@example.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $target = User::factory()->create([
            'name' => 'Client Cible',
            'email' => 'client-cible@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'name' => 'Client Modifié',
            'email' => 'client-modifie@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Client Modifié',
            'email' => 'client-modifie@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Connecté',
            'email' => 'admin-connecte@example.com',
        ]);
    }

    public function test_la_fiche_utilisateur_affiche_le_bon_utilisateur(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Connecté',
            'email' => 'admin-connecte@example.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $target = User::factory()->create([
            'name' => 'Client Cible',
            'email' => 'client-cible@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.show', $target));

        $response->assertOk();
        $response->assertSee('Client Cible');
        $response->assertSee('client-cible@example.com');
        $response->assertSee(route('admin.users.edit', $target), false);
        $response->assertDontSee(route('admin.users.edit', $admin), false);
    }
}
