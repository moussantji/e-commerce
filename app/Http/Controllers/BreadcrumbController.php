<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class BreadcrumbController extends Controller
{
    public function getCategoryFromSlug($slug = null)
    {
        if (!$slug) return null;

        return cache()->remember(
            "category_{$slug}",
            3600,
            fn() =>
            Categories::where('slug', $slug)->where('is_active', true)->first()
        );
    }
}
