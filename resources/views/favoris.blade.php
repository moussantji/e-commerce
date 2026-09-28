@extends('base')

@section('title', 'Mes favoris')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Favoris</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Mes favoris</h1>
            <p>Vos coups de cœur, prêts à passer au panier.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            @livewire('whishlist')
        </div>
    </section>

    @include('partials.footer')
@endsection
