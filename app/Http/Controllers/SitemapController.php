<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Produits;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            ['loc' => route('home'), 'freq' => 'daily', 'prio' => '1.0'],
            ['loc' => route('products'), 'freq' => 'daily', 'prio' => '0.9'],
            ['loc' => route('categories.index'), 'freq' => 'weekly', 'prio' => '0.7'],
            ['loc' => route('client.brands.index'), 'freq' => 'weekly', 'prio' => '0.6'],
            ['loc' => route('conditions'), 'freq' => 'yearly', 'prio' => '0.3'],
            ['loc' => route('confidentialite'), 'freq' => 'yearly', 'prio' => '0.3'],
        ];

        $categories = Categories::where('is_active', true)
            ->where(function ($query) {
                $query->whereHas('products', fn ($productQuery) => $productQuery->where('is_active', true))
                    ->orWhereHas('children', fn ($childQuery) => $childQuery
                        ->where('is_active', true)
                        ->whereHas('products', fn ($productQuery) => $productQuery->where('is_active', true)));
            })
            ->get(['slug', 'updated_at']);

        foreach ($categories as $cat) {
            $urls[] = [
                'loc' => route('categories.show', $cat->slug),
                'freq' => 'weekly',
                'prio' => '0.7',
                'lastmod' => $cat->updated_at?->toAtomString(),
            ];
        }

        foreach (Produits::where('is_active', true)->get(['id', 'name', 'updated_at']) as $p) {
            $urls[] = [
                'loc' => route('produits.show', ['slug' => $p->getSlug(), 'id' => $p->id]),
                'freq' => 'weekly',
                'prio' => '0.8',
                'lastmod' => $p->updated_at?->toAtomString(),
            ];
        }

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /** robots.txt dynamique : URL du sitemap correcte selon l'environnement. */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin/',
            'Disallow: /panier',
            'Disallow: /checkout',
            'Disallow: /commande/',
            'Disallow: /profile',
            'Disallow: /notifications',
            'Disallow: /favoris',
            'Disallow: /paiement/',
            'Disallow: /avis/',
            'Disallow: /api/',
            'Disallow: /vendeur/',
            '',
            'Sitemap: ' . route('sitemap'),
        ];

        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain']);
    }
}
