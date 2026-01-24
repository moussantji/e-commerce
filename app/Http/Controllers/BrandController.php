<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Categories;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        // ✅ Brands actives AVEC 1ère photo chargée
        $brands = Brand::where('is_active', true)
            ->with(['photos' => function ($query) {
                $query->orderBy('created_at')->limit(1);
            }])
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();
        $categories = Categories::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->take(8)
            ->get();
        return view('client.brands.index', [
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }

    public function show(Brand $brand)
    {
        $products = $brand->products()->paginate(12);
        return view('client.brands.show', compact('brand', 'products'));
    }
}
