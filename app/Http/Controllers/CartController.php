<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $categories = Categories::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->take(8)
            ->get();

        return view('cart',[
            'categories'=> $categories
        ]);
    }
}
