@extends('base')

@section('title', 'Produits')

@section('content')
    @include('section-begin')

    @php
        $category = app(\App\Http\Controllers\BreadcrumbController::class)->getCategoryFromSlug(request('category'));
        $searchQuery = request('q', '');
        $tagQuery = request('tag', '');
        $allCats = \App\Models\Categories::whereNull('parent_id')->where('is_active', true)->take(8)->get();
    @endphp

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
            <h1>{{ $category?->name ?? ($searchQuery ? 'Résultats : « ' . $searchQuery . ' »' : 'Tous les produits') }}</h1>
            <p>{{ $category?->description ?? 'Filtres, tri et recherche.' }}</p>
            <div class="cats" style="padding:16px 0 0">
                <a class="cat {{ !$category ? 'hot' : '' }}" href="{{ route('products') }}">Tous</a>
                @foreach ($allCats as $c)
                    <a class="cat {{ $category && $category->id === $c->id ? 'hot' : '' }}"
                        href="{{ route('products', ['category' => $c->slug]) }}">{{ $c->name }}</a>
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
