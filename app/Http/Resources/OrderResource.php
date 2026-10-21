<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        // On précalcule l'hôte une seule fois via le helper request() : aucune
        // closure imbriquée ne dépend plus de la variable $request.
        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');

        return [
            'id' => $this->id,
            'numero' => $this->numero_commande,
            'statut' => $this->statut,
            'statut_label' => $this->statutLabel(),
            'total' => (float) $this->total,
            'sous_total' => (float) $this->sous_total,
            'frais_livraison' => (float) $this->frais_livraison,
            'adresse_livraison' => $this->adresse_livraison,
            'notes' => $this->notes,
            'date' => optional($this->created_at)->format('d/m/Y H:i'),
            'items_count' => $this->whenCounted('produits'),
            'payment_method' => $this->whenLoaded('paiement', fn () => $this->paiement ? [
                'id' => $this->paiement->id,
                'name' => $this->paiement->method_name,
                'provider' => $this->paiement->provider_name,
                'cod' => $this->paiement->isCashOnDelivery(),
                'account_number' => $this->paiement->account_number,
                'instructions' => $this->paiement->instructions,
            ] : null),
            'items' => $this->whenLoaded('produits', fn () => $this->produits->map(function ($p) use ($baseUrl) {
                $photo = $p->getPhoto();
                $img = $photo ? $photo->getImageUrl(200, 200) : asset('assets/img/products/1.png');
                return [
                    'product_id' => $p->id,
                    'name' => $p->name,
                    'image' => str_starts_with($img, 'http') ? $img : $baseUrl . '/' . ltrim($img, '/'),
                    'quantity' => (int) $p->pivot->quantite,
                    'unit_price' => (float) $p->pivot->prix_unitaire,
                    'line_total' => (float) ($p->pivot->total ?? $p->pivot->quantite * $p->pivot->prix_unitaire),
                ];
            })),
        ];
    }

    private function statutLabel(): string
    {
        return \App\Support\OrderStatus::label($this->statut);
    }
}
