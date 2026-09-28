@extends('admin.base')

@section('title', 'Modifier le produit')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.products.index') }}">Produits</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Modifier</span>
        </div>
    </nav>

    <section>
        <div class="wrap">
            <livewire:admin.edit-product :produit_id="$product->id" />
        </div>
    </section>
@endsection
