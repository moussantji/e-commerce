<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Produits;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Produits::query()
            ->where('is_active', true)
            ->with(['category', 'photos'])
            ->withCount('reviews')
            ->withAvg('reviews', 'nb_etoiles');

        // Masquer les ruptures sauf si explicitement demandé
        if (!$request->boolean('include_out_of_stock')) {
            $query->where('stock', '>', 0);
        }

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Uniquement les produits en promotion (prix soldé renseigné)
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price');
        }

        $sort = $request->query('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'popular' => $query->orderByDesc('reviews_count'),
            default => $query->latest(),
        };

        $products = $query->paginate((int) $request->query('per_page', 15));

        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $product = Produits::with(['category', 'photos', 'caracteristiques'])
            ->withCount('reviews')
            ->withAvg('reviews', 'nb_etoiles')
            ->findOrFail($id);

        return new ProductResource($product);
    }
}
