@extends('base-notfondu')

@section('content')
    <main class="main" id="top">
        <!-- ============================================-->
        <!-- <section> begin ============================-->
        @include('section-begin')
        <!-- <section> close ============================-->
        <!-- ============================================-->

        @include('partials.nav')

        @php
            $category = app(\App\Http\Controllers\BreadcrumbController::class)->getCategoryFromSlug(
                request('category'),
            );
            $searchQuery = request('q', ''); // ✅ DÉCLARÉ ICI
            $tagQuery = request('tag', ''); // ✅ DÉCLARÉ
        @endphp

        <!-- ============================================-->
        <!-- <section> begin ============================-->
        <section class="pt-5 pb-9">
            @livewire('produits', ['category' => $categorySlug ?? null, 'search' => $searchQuery, 'tag' => $tagQuery])<!-- end of .container-->
        </section><!-- <section> close ============================-->
        <!-- ============================================-->
    </main>
    @include('partials.footer')
@endsection
