@extends('base')

@section('content')
    <!-- LOADING SCREEN -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-circle"></div>
        <div class="loader-glow"></div>
    </div>
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

        <div class="ecommerce-homepage pt-5 mb-9">

            <!-- ============================================-->
            <!-- <section> begin ============================-->
            <!-- <section> close ============================-->
            <!-- ============================================-->
            <section class="py-0">
                <div class="container-small">
                    <div class="scrollbar scrollbar-nav position-relative">
                        <div class="scroll-track" id="scrollTrack">
                            <div class="scroll-content" id="scrollContent">
                                <a class="icon-nav-item" href="{{ route('products', ['category' => 'deals']) }}">
                                    <div class="icon-container mb-2 bg-warning-subtle">
                                        <span class="fs-4 uil uil-star text-warning"></span>
                                    </div>
                                    <p class="nav-label">Offres</p>
                                </a>
                                <!-- Add your other 10 links here -->
                                <a class="icon-nav-item" href="{{ route('products', ['category' => 'grocery']) }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-shopping-bag"></span></div>
                                    <p class="nav-label">Épicerie</p>
                                </a><a class="icon-nav-item" href="{{ route('products', ['category' => 'mode']) }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-watch-alt"></span></div>
                                    <p class="nav-label">Mode</p>
                                </a><a class="icon-nav-item"
                                    href="{{ route('products', ['category' => 'telephones-portables']) }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-mobile-android"></span></div>
                                    <p class="nav-label">Téléphones</p>
                                </a><a class="icon-nav-item" href="{{ route('products', ['category' => 'electronique']) }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-monitor"></span></div>
                                    <p class="nav-label">Électronique</p>
                                </a><a class="icon-nav-item" href="{{ route('home') }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-estate"></span></div>
                                    <p class="nav-label">Maison</p>
                                </a><a class="icon-nav-item" href="{{ route('products', ['category' => 'dining']) }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-lamp"></span></div>
                                    <p class="nav-label">Salle à manger</p>
                                </a><a class="icon-nav-item" href="{{ route('products', ['category' => 'gift']) }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-gift"></span></div>
                                    <p class="nav-label">Cadeaux</p>
                                </a><a class="icon-nav-item" href="{{ route('products', ['category' => 'tool']) }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-wrench"></span></div>
                                    <p class="nav-label">Outillage</p>
                                </a><a class="icon-nav-item" href="{{ route('products', ['category' => 'travel']) }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-plane-departure"></span></div>
                                    <p class="nav-label">Voyage</p>
                                </a><a class="icon-nav-item" href="{{ route('products', ['category' => 'autres']) }}">
                                    <div class="icon-container mb-2" data-bs-theme="undefined"><span
                                            class="fs-4 uil uil-palette"></span></div>
                                    <p class="nav-label">Autres</p>
                                </a>
                            </div>
                            <!-- Clone will be inserted by JS -->
                            <div class="scroll-content" id="scrollContentClone"></div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- ============================================-->
            <!-- <section> begin ============================-->
            <section class="py-0 px-xl-3">
                <div class="container px-xl-0 px-xxl-3">
                    <div class="row g-3 mb-9">
                        @foreach ($banners->take(3)->reverse() as $index => $banner)
                            @if ($index === 2)
                                {{-- 1ère bannière (full width) --}}
                                <div class="col-12">
                                    <div class="whooping-banner w-100 rounded-3 overflow-hidden">
                                        <div class="bg-holder z-n1 product-bg"
                                            style="background-image:url({{ $banner->getPhoto() ? $banner->getPhoto()->getImageUrl(1315, 1006) : asset('assets/img/banners/whooping_banner.jpg') }});background-position: bottom right;">
                                        </div>
                                        <div class="bg-holder z-n1 shape-bg"
                                            style="background-image:url(../../../assets/img/e-commerce/whooping_banner_shape_2.png);background-position: bottom left;">
                                        </div>
                                        <div class="banner-text" data-bs-theme="light">
                                            <h2 class="text-warning-light fw-bolder fs-lg-3 fs-xxl-2">
                                                {{ $banner->title1_short }}
                                                @if ($banner->percentage)
                                                    <span class="gradient-text">{{ $banner->percentage }}%</span> de
                                                    réduction
                                                @endif
                                            </h2>
                                            <h3 class="fw-bolder fs-lg-5 fs-xxl-3 text-white">
                                                {{ $banner->title2_short }}</h3>
                                        </div>
                                        <a class="btn btn-lg btn-primary rounded-pill banner-button"
                                            href="{{ $banner->button_link }}">Acheter maintenant</a>
                                    </div>
                                </div>
                            @elseif($index === 1)
                                {{-- 2ème bannière --}}
                                <div class="col-12 col-xl-6">
                                    <div class="gift-items-banner w-100 rounded-3 overflow-hidden">
                                        <div class="bg-holder z-n1 banner-bg"
                                            style="background-image:url({{ $banner->getPhoto() ? $banner->getPhoto()->getImageUrl(1315, 1006) : asset('assets/img/banners/gift_items_banner.jpg') }});">
                                        </div>
                                        <div class="banner-text text-md-center">
                                            <h2 class="text-white fw-bolder fs-xl-4">
                                                {{ $banner->title2_short }}
                                                @if ($banner->percentage)
                                                    <span class="gradient-text">{{ $banner->percentage }}% de
                                                        réduction</span>
                                                @endif
                                            </h2>
                                            <a class="btn btn-lg btn-primary rounded-pill banner-button"
                                                href="{{ $banner->button_link }}">Acheter maintenant</a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- 3ème bannière --}}
                                <div class="col-12 col-xl-6">
                                    <div
                                        class="best-in-market-banner d-flex h-100 px-4 px-sm-7 py-5 px-md-11 rounded-3 overflow-hidden">
                                        <div class="bg-holder z-n1 banner-bg"
                                            style="background-image:url({{ $banner->getPhoto() ? $banner->getPhoto()->getImageUrl(1315, 1006) : asset('assets/img/banners/gift_items_banner.jpg') }});">
                                        </div>
                                        <div class="row align-items-center w-sm-100">
                                                <div class="banner-text">
                                                    <h2 class="text-white fw-bolder fs-sm-4 mb-5">
                                                        {{ $banner->title1_short }}<br>
                                                        <span class="fs-7 fs-sm-6">{{ $banner->title2_short }}</span>
                                                    </h2>
                                                    <a class="btn btn-lg btn-warning rounded-pill banner-button"
                                                        href="{{ $banner->button_link }}">Acheter maintenant</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="row g-4 mb-6">
                        <div class="col-12 col-lg-9 col-xxl-10">
                            <div class="d-flex flex-between-center mb-3" data-hidden>
                                <div class="d-flex">
                                    <span class="fas fa-bolt text-warning fs-6"></span>
                                    <h3 class="mx-2">Meilleures offres du jour</h3>
                                    <span class="fas fa-bolt text-warning fs-6"></span>
                                </div>
                                <a class="btn btn-link btn-lg p-0 d-none d-md-block"
                                    href="{{ route('products') }}">Explorer
                                    plus<span class="fas fa-chevron-right fs-9 ms-1"></span>
                                </a>
                            </div>
                            <div class="swiper-theme-container products-slider" data-hidden>
                                <div class="swiper swiper theme-slider"
                                    data-swiper='{
         "slidesPerView":1,
         "spaceBetween":16,
         "autoplay": {
             "delay": 5000,
             "disableOnInteraction": false,
             "pauseOnMouseEnter": true
         },
         "breakpoints":{
             "450":{"slidesPerView":2,"spaceBetween":16},
             "768":{"slidesPerView":3,"spaceBetween":20},
             "1200":{"slidesPerView":4,"spaceBetween":16},
             "1540":{"slidesPerView":5,"spaceBetween":16}
         }
     }'>
                                    <livewire:client.top-deals />

                                </div>
                                <div class="swiper-nav swiper-product-nav">
                                    <div class="swiper-button-next"><span class="fas fa-chevron-right nav-icon"></span>
                                    </div>
                                    <div class="swiper-button-prev"><span class="fas fa-chevron-left nav-icon"></span>
                                    </div>
                                </div>
                            </div><a class="fw-bold d-md-none px-0" href="{{ route('products') }}">Explorer plus<span
                                    class="fas fa-chevron-right fs-9 ms-1"></span></a>
                        </div>
                        <div class="col-lg-3 d-none d-lg-block col-xxl-2">
                            <div class="h-100 position-relative rounded-3 overflow-hidden" data-hidden>
                                <div class="bg-holder"
                                    style="background-image:url(../../../assets/img/e-commerce/4.png);"></div>
                                <!--/.bg-holder-->
                            </div>
                        </div>
                        <div class="col-12 d-lg-none"><a href="#!"><img class="w-100 rounded-3"
                                    src="{{ asset('assets/img/e-commerce/6.png') }}" alt="" /></a></div>
                    </div>
                    <div class="mb-6">
                        <div class="d-flex flex-between-center mb-3" data-hidden>
                            <h3>Électronique populaire</h3><a class="fw-bold d-none d-md-block"
                                href="{{ route('products') }}">Explorer plus<span
                                    class="fas fa-chevron-right fs-9 ms-1"></span></a>
                        </div>
                        <div class="swiper-theme-container products-slider" data-hidden>
                            <div class="swiper swiper theme-slider"
                                data-swiper='{
         "slidesPerView":1,
         "spaceBetween":16,
         "autoplay": {
             "delay": 5000,
             "disableOnInteraction": false,
             "pauseOnMouseEnter": true
         },
         "breakpoints":{
             "450":{"slidesPerView":2,"spaceBetween":16},
             "768":{"slidesPerView":3,"spaceBetween":20},
             "1200":{"slidesPerView":4,"spaceBetween":16},
             "1540":{"slidesPerView":5,"spaceBetween":16}
         }
     }'>
                                <livewire:client.top-electronics />
                            </div>
                            <div class="swiper-nav">
                                <div class="swiper-button-next"><span class="fas fa-chevron-right nav-icon"></span>
                                </div>
                                <div class="swiper-button-prev"><span class="fas fa-chevron-left nav-icon"></span></div>
                            </div>
                        </div><a class="fw-bold d-md-none" href="{{ route('products') }}">Explorer plus<span
                                class="fas fa-chevron-right fs-9 ms-1"></span></a>
                    </div>
                    <div class="mb-6">
                        <div class="d-flex flex-between-center mb-3" data-hidden>
                            <h3>Meilleures offres</h3><a class="fw-bold d-none d-md-block"
                                href="{{ route('products') }}">Explorer plus<span
                                    class="fas fa-chevron-right fs-9 ms-1"></span></a>
                        </div>
                        <div class="swiper-theme-container products-slider" data-hidden>
                            <div class="swiper swiper theme-slider"
                                data-swiper='{
         "slidesPerView":1,
         "spaceBetween":16,
         "autoplay": {
             "delay": 5000,
             "disableOnInteraction": false,
             "pauseOnMouseEnter": true
         },
         "breakpoints":{
             "450":{"slidesPerView":2,"spaceBetween":16},
             "768":{"slidesPerView":3,"spaceBetween":20},
             "1200":{"slidesPerView":4,"spaceBetween":16},
             "1540":{"slidesPerView":5,"spaceBetween":16}
         }
     }'>
                                <livewire:client.best-offers />
                            </div>
                            <div class="swiper-nav">
                                <div class="swiper-button-next"><span class="fas fa-chevron-right nav-icon"></span>
                                </div>
                                <div class="swiper-button-prev"><span class="fas fa-chevron-left nav-icon"></span></div>
                            </div>
                        </div><a class="fw-bold d-md-none" href="{{ route('products') }}">Explorer plus<span
                                class="fas fa-chevron-right fs-9 ms-1"></span></a>
                    </div>
                    @if (!isset($user))
                        <div class="row flex-center mb-15 mt-11 gy-6">
                            <div class="col-auto"><img class="d-dark-none"
                                    src="{{ asset('assets/img/spot-illustrations/light_30.png') }}" alt=""
                                    width="305" /><img class="d-light-none"
                                    src="{{ asset('assets/img/spot-illustrations/dark_30.png') }}" alt=""
                                    width="305" />
                            </div>
                            <div class="col-auto">
                                <div class="text-center text-lg-start">
                                    <h3 class="text-body-highlight mb-2"><span class="fw-semibold">Vous voulez vivre
                                        </span>la meilleure expérience client ?</h3>
                                    <h1 class="display-3 fw-semibold mb-4">Devenez <span
                                            class="text-primary fw-bolder">membre</span> aujourd'hui !</h1><a
                                        class="btn btn-lg btn-primary px-7"
                                        href="{{ route('register') }}">S'inscrire<span
                                            class="fas fa-chevron-right ms-2 fs-9"></span></a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div><!-- end of .container-->
            </section><!-- <section> close ============================-->
            <!-- ============================================-->

        </div>

        @include('partials.footer')

    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->
@endsection
