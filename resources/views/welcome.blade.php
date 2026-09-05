@extends('base')

@section('title', 'Accueil - E-Commerce Ultra Premium')

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

        <!-- Header Topbar -->
        @include('section-begin')

        <!-- Desktop Navigation Bar (Masquée sur mobile pour éviter les boutons empilés) -->
        @include('partials.nav')

        <div class="ecommerce-homepage pt-3 pb-8">

            <!-- ============================================-->
            <!-- 1. CATEGORIES BAR (Pastilles tactiles néo-luxe) -->
            <!-- ============================================-->
            <section class="py-2 mb-4">
                <div class="container-small">
                    <div class="scrollbar scrollbar-nav position-relative">
                        <div class="scroll-track" id="scrollTrack">
                            <div class="scroll-content" id="scrollContent">
                                <a class="icon-nav-item" href="{{ route('products', ['category' => 'deals']) }}">
                                    <div class="icon-container mb-2">
                                        <span class="fs-5 fas fa-bolt" style="color: #38BDF8;"></span>
                                    </div>
                                    <p class="nav-label">Offres Flash</p>
                                </a>
                                <a class="icon-nav-item" href="{{ route('products', ['category' => 'telephones-portables']) }}">
                                    <div class="icon-container mb-2">
                                        <span class="fs-5 fas fa-mobile-screen-button"></span>
                                    </div>
                                    <p class="nav-label">Téléphones</p>
                                </a>
                                <a class="icon-nav-item" href="{{ route('products', ['category' => 'electronique']) }}">
                                    <div class="icon-container mb-2">
                                        <span class="fs-5 fas fa-headphones"></span>
                                    </div>
                                    <p class="nav-label">Audio & Tech</p>
                                </a>
                                <a class="icon-nav-item" href="{{ route('products', ['category' => 'mode']) }}">
                                    <div class="icon-container mb-2">
                                        <span class="fs-5 fas fa-clock"></span>
                                    </div>
                                    <p class="nav-label">Montres</p>
                                </a>
                                <a class="icon-nav-item" href="{{ route('products', ['category' => 'dining']) }}">
                                    <div class="icon-container mb-2">
                                        <span class="fs-5 fas fa-laptop"></span>
                                    </div>
                                    <p class="nav-label">Ordinateurs</p>
                                </a>
                                <a class="icon-nav-item" href="{{ route('products', ['category' => 'gift']) }}">
                                    <div class="icon-container mb-2">
                                        <span class="fs-5 fas fa-gift"></span>
                                    </div>
                                    <p class="nav-label">Cadeaux</p>
                                </a>
                                <a class="icon-nav-item" href="{{ route('products', ['category' => 'travel']) }}">
                                    <div class="icon-container mb-2">
                                        <span class="fs-5 fas fa-plane-departure"></span>
                                    </div>
                                    <p class="nav-label">Voyage</p>
                                </a>
                                <a class="icon-nav-item" href="{{ route('categories.index') }}">
                                    <div class="icon-container mb-2">
                                        <span class="fs-5 fas fa-layer-group"></span>
                                    </div>
                                    <p class="nav-label">Tout voir</p>
                                </a>
                            </div>
                            <!-- Clone JS -->
                            <div class="scroll-content" id="scrollContentClone"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============================================-->
            <!-- 2. HERO SHOWCASE BANNER (Photos cadrées proprement) -->
            <!-- ============================================-->
            <section class="py-0 px-xl-3 mb-6">
                <div class="container-small">
                    @if(isset($banners) && count($banners) > 0)
                        @php $mainBanner = $banners->first(); @endphp
                        <div class="neo-hero-banner p-4 p-md-6 rounded-4 position-relative overflow-hidden mb-5">
                            <div class="row align-items-center gy-4">
                                <div class="col-12 col-md-7 z-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill text-uppercase fw-bold fs-10 mb-3 d-inline-flex align-items-center">
                                        <i class="fas fa-bolt me-2"></i> {{ $mainBanner->title1_short ?? 'Collection Exclusivité' }}
                                        @if($mainBanner->percentage)
                                            <span class="ms-2 text-warning fw-bolder">-{{ $mainBanner->percentage }}%</span>
                                        @endif
                                    </span>
                                    <h1 class="display-6 fw-bold text-white mb-3">
                                        {{ $mainBanner->title2_short ?? 'L’Expérience E-Commerce Redéfinie' }}
                                    </h1>
                                    <p class="text-body-tertiary fs-9 mb-4" style="max-width: 480px;">
                                        Découvrez les dernières innovations, montres de luxe, appareils haute technologie et accessoires haut de gamme.
                                    </p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <a href="{{ $mainBanner->button_link ?? route('products') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                            Explorer le catalogue <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                        <a href="{{ route('products') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 fs-9">
                                            Offres du moment
                                        </a>
                                    </div>
                                </div>
                                <div class="col-12 col-md-5 text-center z-2">
                                    <div class="neo-hero-image-wrapper p-3 d-flex align-items-center justify-content-center">
                                        <img class="img-fluid hero-contained-img" 
                                            src="{{ $mainBanner->getPhoto() ? $mainBanner->getPhoto()->getImageUrl(800, 800) : asset('public/mockups/prop3_mobile.png') }}" 
                                            alt="Hero Banner Product" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Fallback Banner if no banner in DB --}}
                        <div class="neo-hero-banner p-4 p-md-6 rounded-4 position-relative overflow-hidden mb-5">
                            <div class="row align-items-center gy-4">
                                <div class="col-12 col-md-7 z-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill text-uppercase fw-bold fs-10 mb-3 d-inline-flex align-items-center">
                                        <i class="fas fa-bolt me-2"></i> Édition Neo-Luxe 2026
                                    </span>
                                    <h1 class="display-6 fw-bold text-white mb-3">
                                        L’Expérience E-Commerce Redéfinie
                                    </h1>
                                    <p class="text-body-tertiary fs-9 mb-4" style="max-width: 480px;">
                                        Découvrez nos collections high-tech, montres connectées et accessoires d'exception avec garantie et livraison express.
                                    </p>
                                    <a href="{{ route('products') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                        Découvrir les nouveautés <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                                <div class="col-12 col-md-5 text-center z-2">
                                    <div class="neo-hero-image-wrapper p-3 d-flex align-items-center justify-content-center">
                                        <img class="img-fluid hero-contained-img" src="{{ asset('mockups/prop3_mobile.png') }}" alt="Hero Product" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <!-- ============================================-->
            <!-- 3. DIRECT PRODUCT SECTION 1 : TOP DEALS DU JOUR -->
            <!-- ============================================-->
            <section class="py-0 px-xl-3 mb-6">
                <div class="container-small">
                    <div class="d-flex flex-between-center mb-4 pb-2 border-bottom border-translucent">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fas fa-fire fs-7" style="color: #38BDF8;"></span>
                            <h3 class="mb-0 fw-bold fs-7 fs-md-6 text-white">Meilleures offres du jour</h3>
                        </div>
                        <a class="btn btn-link text-primary p-0 fw-bold fs-9" href="{{ route('products') }}">
                            Explorer plus <span class="fas fa-chevron-right fs-10 ms-1"></span>
                        </a>
                    </div>

                    <div class="swiper-theme-container products-slider mb-5">
                        <div class="swiper swiper theme-slider"
                            data-swiper='{
                                "slidesPerView":2,
                                "spaceBetween":14,
                                "autoplay": {
                                    "delay": 5000,
                                    "disableOnInteraction": false,
                                    "pauseOnMouseEnter": true
                                },
                                "breakpoints":{
                                    "576":{"slidesPerView":2,"spaceBetween":16},
                                    "768":{"slidesPerView":3,"spaceBetween":18},
                                    "1200":{"slidesPerView":4,"spaceBetween":20},
                                    "1540":{"slidesPerView":5,"spaceBetween":20}
                                }
                            }'>
                            <livewire:client.top-deals />
                        </div>
                        <div class="swiper-nav swiper-product-nav">
                            <div class="swiper-button-next"><span class="fas fa-chevron-right nav-icon"></span></div>
                            <div class="swiper-button-prev"><span class="fas fa-chevron-left nav-icon"></span></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============================================-->
            <!-- 4. DIRECT PRODUCT SECTION 2 : ÉLECTRONIQUE POPULAIRE -->
            <!-- ============================================-->
            <section class="py-0 px-xl-3 mb-6">
                <div class="container-small">
                    <div class="d-flex flex-between-center mb-4 pb-2 border-bottom border-translucent">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fas fa-microchip fs-7" style="color: #38BDF8;"></span>
                            <h3 class="mb-0 fw-bold fs-7 fs-md-6 text-white">Électronique & High-Tech</h3>
                        </div>
                        <a class="btn btn-link text-primary p-0 fw-bold fs-9" href="{{ route('products', ['category' => 'electronique']) }}">
                            Voir tout <span class="fas fa-chevron-right fs-10 ms-1"></span>
                        </a>
                    </div>

                    <div class="swiper-theme-container products-slider mb-5">
                        <div class="swiper swiper theme-slider"
                            data-swiper='{
                                "slidesPerView":2,
                                "spaceBetween":14,
                                "autoplay": {
                                    "delay": 6000,
                                    "disableOnInteraction": false,
                                    "pauseOnMouseEnter": true
                                },
                                "breakpoints":{
                                    "576":{"slidesPerView":2,"spaceBetween":16},
                                    "768":{"slidesPerView":3,"spaceBetween":18},
                                    "1200":{"slidesPerView":4,"spaceBetween":20},
                                    "1540":{"slidesPerView":5,"spaceBetween":20}
                                }
                            }'>
                            <livewire:client.top-electronics />
                        </div>
                        <div class="swiper-nav">
                            <div class="swiper-button-next"><span class="fas fa-chevron-right nav-icon"></span></div>
                            <div class="swiper-button-prev"><span class="fas fa-chevron-left nav-icon"></span></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============================================-->
            <!-- 5. DIRECT PRODUCT SECTION 3 : MEILLEURES OFFRES -->
            <!-- ============================================-->
            <section class="py-0 px-xl-3 mb-6">
                <div class="container-small">
                    <div class="d-flex flex-between-center mb-4 pb-2 border-bottom border-translucent">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fas fa-star fs-7" style="color: #F59E0B;"></span>
                            <h3 class="mb-0 fw-bold fs-7 fs-md-6 text-white">Sélections Recommandées</h3>
                        </div>
                        <a class="btn btn-link text-primary p-0 fw-bold fs-9" href="{{ route('products') }}">
                            Explorer plus <span class="fas fa-chevron-right fs-10 ms-1"></span>
                        </a>
                    </div>

                    <div class="swiper-theme-container products-slider mb-5">
                        <div class="swiper swiper theme-slider"
                            data-swiper='{
                                "slidesPerView":2,
                                "spaceBetween":14,
                                "autoplay": {
                                    "delay": 5500,
                                    "disableOnInteraction": false,
                                    "pauseOnMouseEnter": true
                                },
                                "breakpoints":{
                                    "576":{"slidesPerView":2,"spaceBetween":16},
                                    "768":{"slidesPerView":3,"spaceBetween":18},
                                    "1200":{"slidesPerView":4,"spaceBetween":20},
                                    "1540":{"slidesPerView":5,"spaceBetween":20}
                                }
                            }'>
                            <livewire:client.best-offers />
                        </div>
                        <div class="swiper-nav">
                            <div class="swiper-button-next"><span class="fas fa-chevron-right nav-icon"></span></div>
                            <div class="swiper-button-prev"><span class="fas fa-chevron-left nav-icon"></span></div>
                        </div>
                    </div>

                    @if (!isset($user) && !auth()->check())
                        <div class="neo-membership-card p-5 rounded-4 my-8 text-center text-md-start">
                            <div class="row align-items-center gy-4">
                                <div class="col-12 col-md-8">
                                    <h3 class="text-white fw-bold mb-2">Vivez l'expérience e-commerce ultime</h3>
                                    <p class="text-body-tertiary fs-9 mb-0">
                                        Rejoignez nos membres privilégiés et profitez de réductions exclusives, du suivi de vos commandes en temps réel et d'offres prioritaires.
                                    </p>
                                </div>
                                <div class="col-12 col-md-4 text-md-end">
                                    <a class="btn btn-primary btn-lg rounded-pill px-6 fw-bold shadow-sm" href="{{ route('register') }}">
                                        S'inscrire gratuitement <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

        </div>

        @include('partials.footer')

    </main>

    <style>
        /* HERO BANNER STYLING PROPOSITION 3 */
        .neo-hero-banner {
            background: linear-gradient(135deg, #0E1524 0%, #131D31 100%);
            border: 1px solid rgba(56, 189, 248, 0.28);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.7), 0 0 25px rgba(56, 189, 248, 0.12);
        }

        .neo-hero-image-wrapper {
            background: rgba(8, 12, 20, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            max-height: 280px;
            overflow: hidden;
        }

        .hero-contained-img {
            max-height: 230px;
            max-width: 85%;
            object-fit: contain;
            filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.6));
            transition: transform 0.4s ease;
        }

        .neo-hero-banner:hover .hero-contained-img {
            transform: scale(1.05);
        }

        .neo-membership-card {
            background: linear-gradient(135deg, rgba(14, 21, 36, 0.95) 0%, rgba(18, 27, 45, 0.95) 100%);
            border: 1px solid rgba(56, 189, 248, 0.25);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
    </style>
@endsection
