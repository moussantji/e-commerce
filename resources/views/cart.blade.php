@extends('base')

@section('title', 'Panier')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('products') }}">Produits</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Panier</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Mon panier</h1>
            <p>Vérifiez vos articles, appliquez un code promo puis validez votre commande.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <livewire:cart />
        </div>
    </section>

    @include('partials.footer')
@endsection
