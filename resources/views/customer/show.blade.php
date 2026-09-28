@extends('base')

@section('title', $produit->name)

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a id="crumCat"
                href="{{ route('products') }}">{{ optional($produit->category)->name ?? 'Produits' }}</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here" id="crumNom">{{ $produit->name }}</span>
        </div>
    </nav>

    <section>
        <div class="wrap">
            @livewire('produit-detail', ['product' => $produit, 'slug' => $slug])
        </div>
    </section>

    @include('partials.footer')
@endsection
