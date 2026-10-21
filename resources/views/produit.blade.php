@extends('base')

@php
    $category = app(\App\Http\Controllers\BreadcrumbController::class)->getCategoryFromSlug(request('category'));
    $searchQuery = trim((string) request('q', ''));
    $tagQuery = trim((string) request('tag', ''));
    $allCats = \App\Models\Categories::whereNull('parent_id')->where('is_active', true)->take(8)->get();

    $pageHeading = $category?->name
        ?? ($searchQuery !== ''
            ? 'Résultats : « ' . $searchQuery . ' »'
            : ($tagQuery !== ''
                ? 'Tag : ' . $tagQuery
                : (request()->filled('promo') ? 'Promotions' : 'Tous les produits')));

    $pageTitle = $category?->name
        ?? ($searchQuery !== ''
            ? 'Recherche : « ' . \Illuminate\Support\Str::limit($searchQuery, 40, '…') . ' »'
            : ($tagQuery !== ''
                ? 'Tag : ' . \Illuminate\Support\Str::limit($tagQuery, 40, '…')
                : (request()->filled('promo') ? 'Promotions' : 'Tous les produits')));

    $pageDescription = $category
        ? ($category->description ?: 'Découvrez notre sélection ' . $category->name . ' au meilleur prix, livrée partout au Mali.')
        : ($searchQuery !== ''
            ? 'Résultats de recherche pour « ' . $searchQuery . ' » sur ' . config('app.name') . '.'
            : ($tagQuery !== ''
                ? 'Produits associés au tag « ' . $tagQuery . ' » sur ' . config('app.name') . '.'
                : (request()->filled('promo')
                    ? 'Découvrez les promotions et offres flash de ' . config('app.name') . ', avec livraison rapide au Mali.'
                    : 'Parcourez tous les produits disponibles sur ' . config('app.name') . ' : électronique, mode, maison et bien plus. Livraison rapide au Mali.')));

    $hasFilter = $searchQuery !== ''
        || $tagQuery !== ''
        || request()->filled('brands')
        || request()->filled('promo')
        || request()->filled('sort');
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDescription)
@section('canonical', $category ? route('categories.show', $category->slug) : url()->current())
@section('robots', $hasFilter ? 'noindex, follow' : 'index, follow, max-image-preview:large')
@if ($category)
    @section('og_image_alt', $category->name)
@endif

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ $category?->name ?? 'Produits' }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>{{ $pageHeading }}</h1>
            <p>{{ $category?->description ?? 'Filtres, tri et recherche.' }}</p>
            <div class="cats" style="padding:16px 0 0">
                <a class="cat {{ !$category ? 'hot' : '' }}" href="{{ route('products') }}">Tous</a>
                @foreach ($allCats as $c)
                    <a class="cat {{ $category && $category->id === $c->id ? 'hot' : '' }}"
                        href="{{ route('categories.show', $c->slug) }}">{{ $c->name }}</a>
                @endforeach
                <a class="cat hot" href="{{ route('products', ['promo' => 1]) }}">Promos</a>
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            @livewire('produits', ['category' => $category?->slug, 'search' => $searchQuery, 'tag' => $tagQuery])
        </div>
    </section>

    @include('partials.footer')
@endsection
