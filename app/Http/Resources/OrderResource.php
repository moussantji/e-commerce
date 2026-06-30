<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
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
            'items' => $this->whenLoaded('produits', fn () => $this->produits->map(function ($p) {
                $photo = $p->getPhoto();
                $img = $photo ? $photo->getImageUrl(200, 200) : asset('assets/img/products/1.png');
                return [
                    'product_id' => $p->id,
                    'name' => $p->name,
                    'image' => str_starts_with($img, 'http') ? $img : rtrim($request->getSchemeAndHttpHost(), '/') . '/' . ltrim($img, '/'),
                    'quantity' => (int) $p->pivot->quantite,
                    'unit_price' => (float) $p->pivot->prix_unitaire,
                    'line_total' => (float) ($p->pivot->total ?? $p->pivot->quantite * $p->pivot->prix_unitaire),
                ];
            })),
        ];
    }

    private function statutLabel(): string
    {
        return match ($this->statut) {
            'en_attente' => 'En attente',
            'traitement' => 'En traitement',
            'expedie' => 'Expédiée',
            'livre' => 'Livrée',
            'annule' => 'Annulée',
            default => ucfirst((string) $this->statut),
        };
    }
}
