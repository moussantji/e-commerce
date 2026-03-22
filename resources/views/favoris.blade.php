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
        <div class="container-small cart">
            <nav class="mb-3" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home me-1"></i>Acceuil</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-heart me-1"></i>Wishlist</li>
                </ol>
            </nav>
            @livewire('whishlist')
        </div><!-- end of .container-->
    </section><!-- <section> close ============================-->
    <!-- ============================================-->

    @include('partials.footer')
@endsection
