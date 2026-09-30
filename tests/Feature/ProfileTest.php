<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this
            ->actingAs($user)
            ->put('/profile', [
                'name' => 'Test User',
                'prenom' => 'Test',
                'tel' => '70000000',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('Test', $user->prenom);
        // Numéro normalisé au format international
        $this->assertSame('22370000000', $user->tel);
    }

    public function test_email_is_locked_for_customers(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this
            ->actingAs($user)
            ->put('/profile', [
                'name' => 'Test User',
                'email' => 'pirate@example.com',
            ]);

        $response->assertSessionHasNoErrors();

        // L'email ne bouge pas (identifiant verrouillé) et reste vérifié.
        $this->assertNotSame('pirate@example.com', $user->refresh()->email);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
