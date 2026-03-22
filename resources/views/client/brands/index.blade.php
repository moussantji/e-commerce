@extends('base')

@section('title', ' ')

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
            <div class="container-small">
                <nav class="mb-3" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}"><i class="fas fa-home me-1"></i>Accueil</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-tag me-1"></i>Toutes les marques
                        </li>
                    </ol>
                </nav>

                <h2 class="mb-1">All Stores</h2>
                <p class="mb-5 text-body-tertiary fw-semibold">Essential for a better life</p>
                <div class="row gx-3 gy-5">
                    @foreach ($brands as $brand)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 hover-actions-trigger btn-reveal-trigger">
                            <div class="border border-translucent d-flex flex-center rounded-3 mb-3 p-4"
                                style="height:180px;">
                                @if ($brand->getPhoto())
                                    <img class="mw-100" src="{{ $brand->getPhoto()->getImageUrl(180, 180) }}"
                                        alt="{{ $brand->name }}" />
                                @else
                                    <div class="bg-light d-flex flex-center rounded-2" style="width:100%;height:100%;">
                                        <span class="fas fa-image fs-3 text-body-tertiary"></span>
                                    </div>
                                @endif
                            </div>

                            <h5 class="mb-2">
                                <a href="{{ route('products', ['brands' => $brand->id]) }}"
                                    class="text-decoration-none {{ request('brands') == $brand->id ? 'text-primary fw-bold' : '' }}">
                                    {{ $brand->name }}
                                </a>
                            </h5>

                            <div class="mb-1 fs-9">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span
                                        class="fa {{ $i <= 4.2 ? 'fa-star text-warning' : 'fa-regular fa-star text-warning-light' }}"></span>
                                @endfor
                            </div>

                            <p class="text-body-quaternary fs-9 mb-2 fw-semibold">
                                ({{ $brand->products_count }} {{ Str::plural('produit', $brand->products_count) }})
                            </p>

                            <a class="btn btn-link p-0" href="{{ route('products', ['brands' => $brand->id]) }}">
                                Visit Store<span class="fas fa-chevron-right ms-1 fs-10"></span>
                            </a>

                            <div class="hover-actions top-0 end-0 mt-2 me-3">
                                <div class="btn-reveal-trigger">
                                    <button
                                        class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal lh-1 bg-body-highlight rounded-1"
                                        type="button" data-bs-toggle="dropdown">
                                        <span class="fas fa-ellipsis-h fs-9"></span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end py-2">
                                        <a class="dropdown-item"
                                            href="{{ route('products') }}?brand={{ $brand->slug }}">View Products</a>
                                        @if ($brand->website)
                                            <a class="dropdown-item" href="{{ $brand->website }}" target="_blank">Visit
                                                Website</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>
            </div><!-- end of .container-->
        </section><!-- <section> close ============================-->
        <!-- ============================================-->

        @include('partials.footer')

    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->


@endsection
