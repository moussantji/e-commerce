@extends('base')

@section('title', 'Toutes les marques')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Marques</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Toutes les marques</h1>
            <p>Les boutiques et fabricants du catalogue.</p>
        </div>
    </section>

    <section>
        <div class="shop-wrap">
            <div class="grid">
                @forelse($brands as $brand)
                    @php
                        $img = $brand->getPhoto() ? $brand->getPhoto()->getImageUrl(300, 300) : null;
                        $bUrl = route('products', ['brands' => $brand->id]);
                    @endphp
                    <article class="card rv in">
                        <div class="thumb"
                            style="@if ($img) background-image:url('{{ $img }}');background-size:contain;background-repeat:no-repeat; @endif">
                            @if (!$img)
                                <svg class="ic" aria-hidden="true">
                                    <use href="#i-b2-tag" />
                                </svg>
                            @endif
                        </div>
                        <div class="body">
                            <div class="name">{{ $brand->name }}</div>
                            <div class="was">{{ $brand->products_count }} {{ Str::plural('produit', $brand->products_count) }}</div>
                            <a href="{{ $bUrl }}" class="add">Visiter la boutique</a>
                        </div>
                    </article>
                @empty
                    <p class="empty">Aucune marque pour le moment.</p>
                @endforelse
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
