<?php

namespace Tests\Feature;

use App\Livewire\ProduitDetail;
use App\Models\AvisClient;
use App\Models\Produits as Product;
use App\Models\ReviewReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use Tests\TestCase;

class ReviewReplyTest extends TestCase
{
    use RefreshDatabase;

    private function reviewFor(Product $product, User $author): AvisClient
    {
        return AvisClient::create([
            'produits_id' => $product->id,
            'user_id' => $author->id,
            'note' => 5,
            'nb_etoiles' => 5,
            'commentaire' => 'Très bon produit.',
        ]);
    }

    public function test_un_client_peut_repondre_a_un_avis_et_imbriquer_des_reponses(): void
    {
        $product = Product::factory()->create();
        $author = User::factory()->create(['role' => 'customer']);
        $replier = User::factory()->create(['role' => 'customer']);
        $review = $this->reviewFor($product, $author);

        $component = Livewire::actingAs($replier)
            ->test(ProduitDetail::class, ['product' => $product])
            ->call('startReply', $review->id)
            ->set('replyMessage', 'Je confirme, excellent produit.')
            ->call('submitReply');

        $reply = ReviewReply::where('avis_client_id', $review->id)->whereNull('parent_id')->first();
        $this->assertNotNull($reply);
        $this->assertSame($replier->id, $reply->user_id);

        $component
            ->call('startReply', $review->id, $reply->id)
            ->set('replyMessage', 'Merci pour votre retour !')
            ->call('submitReply');

        $nested = ReviewReply::where('parent_id', $reply->id)->first();
        $this->assertNotNull($nested);
        $this->assertSame($reply->id, $nested->parent_id);

        $component
            ->assertSee('Je confirme, excellent produit.')
            ->assertSee('Merci pour votre retour !');

        // Les réponses aux réponses restent au même niveau visuel (niveau 1).
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $component->html());
        $xpath = new \DOMXPath($dom);
        $replyNodes = $xpath->query('//div[@class="rreply"]');

        $this->assertCount(2, $replyNodes);
        $this->assertSame(
            $replyNodes->item(0)->parentNode->getNodePath(),
            $replyNodes->item(1)->parentNode->getNodePath()
        );
        $this->assertSame('review-replies', $replyNodes->item(1)->parentNode->getAttribute('class'));

        foreach ($replyNodes as $node) {
            $this->assertFalse($node->hasAttribute('style'));
        }
    }

    public function test_api_retourne_et_cree_des_reponses_imbriquees(): void
    {
        $product = Product::factory()->create();
        $author = User::factory()->create(['role' => 'customer']);
        $replier = User::factory()->create(['role' => 'customer']);
        $review = $this->reviewFor($product, $author);

        Sanctum::actingAs($replier);

        $first = $this->postJson("/api/products/{$product->id}/reviews/{$review->id}/replies", [
            'message' => 'Première réponse API.',
        ])->assertCreated();

        $parentId = $first->json('data.id');
        $this->assertNotNull($parentId);

        $second = $this->postJson("/api/products/{$product->id}/reviews/{$review->id}/replies", [
            'message' => 'Réponse imbriquée API.',
            'parent_id' => $parentId,
        ])->assertCreated();

        $this->assertSame($parentId, $second->json('data.parent_id'));

        $this->getJson("/api/products/{$product->id}/reviews")
            ->assertOk()
            ->assertJsonPath('data.0.id', $review->id)
            ->assertJsonPath('data.0.replies.0.message', 'Première réponse API.')
            ->assertJsonPath('data.0.replies.0.replies.0.message', 'Réponse imbriquée API.');
    }
}
