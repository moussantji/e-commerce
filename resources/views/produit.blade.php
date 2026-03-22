@extends('base')

@section('content')
    <!-- ============================================-->
    <!-- <section> begin ============================-->
    @include('section-begin')
    <!-- <section> close ============================-->
    <!-- ============================================-->

    @include('partials.nav')

    <!-- ============================================-->
    <!-- <section> begin ============================-->
    <section class="pt-5 pb-9">
        @php
            $category = app(\App\Http\Controllers\BreadcrumbController::class)->getCategoryFromSlug(
                request('category'),
            );
            $searchQuery = request('q', ''); // ✅ DÉCLARÉ ICI
            $tagQuery = request('tag', ''); // ✅ DÉCLARÉ
        @endphp


        <nav class="mb-3 container-small" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home me-1"></i>Accueil</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('products') }}"><i class="fas fa-shopping-bag me-1"></i>Produits</a>
                </li>
                @if ($category)
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                @elseif($searchQuery)
                    <li class="breadcrumb-item active">Recherche "{{ $searchQuery }}"</li>
                @elseif($tagQuery)
                    <!-- ✅ UTILISÉ -->
                    <li class="breadcrumb-item active">Tag "{{ $tagQuery }}"</li>
                @else
                    <li class="breadcrumb-item active">Tous les produits</li>
                @endif
            </ol>
        </nav>
        @livewire('produits', ['category' => $categorySlug ?? null, 'search' => $searchQuery, 'tag' => $tagQuery])<!-- end of .container-->
    </section><!-- <section> close ============================-->
    <!-- ============================================-->

    @include('partials.footer')
@endsection
