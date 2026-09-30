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
        ];

        foreach (Categories::where('is_active', true)->get(['slug', 'updated_at']) as $cat) {
            $urls[] = [
                'loc' => route('categories.show', $cat->slug),
                'freq' => 'weekly',
                'prio' => '0.7',
            ];
        }

        foreach (Produits::where('is_active', true)->get(['id', 'name', 'updated_at']) as $p) {
            $urls[] = [
                'loc' => route('produits.show', ['slug' => $p->getSlug(), 'id' => $p->id]),
                'freq' => 'weekly',
                'prio' => '0.8',
            ];
        }

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
