<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientCategoryController extends Controller
{
    public function index()  // ✅ Ajoute cette méthode
    {
        $categories = Categories::with(['children', 'photos'])
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->get();

        return view('client.categories.index', compact('categories'));
    }

    public function show(Categories $category)
    {

        // Sous-catégories de cette catégorie
        $subcategories = $category->children()
            ->with(['photos'])
            ->where('is_active', true)
            ->get();

        $categories = Categories::with(['children', 'photos'])
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->get();

        return view('client.categories.index', compact(
            'category',
            'subcategories',
            'categories',
        ));
    }
}
