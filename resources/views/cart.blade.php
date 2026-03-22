@extends('base')

@section('content')
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">

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
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home me-1"></i>Acceuil</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><i
                                class="fas fa-shopping-cart me-1"></i>Panier</li>
                    </ol>
                </nav>
                <h2 class="mb-6"><i class="fas fa-shopping-cart me-1"></i>Panier</h2>
                <livewire:cart />
            </div><!-- end of .container-->
        </section><!-- <section> close ============================-->
        <!-- ============================================-->



        @include('partials.footer')
    @endsection
