<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    /**
     * Page d'aide et de support : questions fréquentes et contacts.
     */
    public function aide(): View
    {
        return view('pages.aide', [
            'categories' => Categories::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->take(8)
                ->get(),
        ]);
    }
}
