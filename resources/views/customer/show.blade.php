@extends('base')

@section('title', $produit->name)

@section('content')

    <!-- ============================================-->
    <!-- <section> begin ============================-->
    @include('section-begin')
    <!-- <section> close ============================-->
    <!-- ============================================-->

    @include('partials.nav')


    @livewire('produit-detail', ['product' => $produit, 'slug' => $slug])

    @include('partials.footer')

    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

@endsection
