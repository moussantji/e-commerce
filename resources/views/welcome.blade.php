@extends('base')

@section('title', 'AETHERA - Uncompromising Sound & Tech')

@section('content')
    <!-- LOADING SCREEN -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-circle"></div>
        <div class="loader-glow"></div>
    </div>

    <div class="cyber-grid-bg"></div>

    <main class="main" id="top">

        <!-- Top Navigation Bar -->
        @include('section-begin')

        <!-- Container Content -->
        <div class="container pt-3 pb-8">

            <!-- ============================================-->
            <!-- 1. HERO SHOWCASE SECTION (Identique à la photo) -->
            <!-- ============================================-->
            <section class="hero-section">
                <div class="hero-layout">
                    <!-- Left Headline & Action Buttons -->
                    <div class="hero-left">
                        <h1 class="hero-headline">
                            AETHERA <span class="highlight">NOVA</span>.<br>
                            UNCOMPROMISING<br>
                            SOUND.
                        </h1>
                        <div class="hero-buttons-group">
                            <a href="{{ route('products') }}" class="btn-pill-explore">Explore</a>
                            <a href="{{ route('products') }}" class="btn-pill-preorder">Pre-Order Now</a>
                        </div>
                    </div>

                    <!-- Right Holographic Product Stage -->
                    <div class="hero-showcase-stage">
                        <div class="hologram-stage-glow"></div>
                        <img src="{{ asset('mockups/prop1_mobile.png') }}" alt="Aethera Nova Headphones" class="hero-headphone-img" style="border-radius: 14px;">
                        
                        <!-- Vertical Slide Dots -->
                        <div class="slider-vertical-dots">
                            <span class="v-dot active"></span>
                            <span class="v-dot"></span>
                            <span class="v-dot"></span>
                            <span class="v-dot"></span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============================================-->
            <!-- 2. CURATED COLLECTION PRODUCT GRID (4 Cartes de la photo) -->
            <!-- ============================================-->
            <section class="featured-products-section mb-6" id="tech">
                <div class="section-heading-row">
                    <span class="section-label">Curated Collection</span>
                    <a href="{{ route('products') }}" class="text-decoration-none fs-9 fw-bold" style="color: #00E5FF;">
                        See All <i class="fas fa-chevron-right ms-1 fs-10"></i>
                    </a>
                </div>

                <div class="products-grid-container">

                    <!-- CARD 1: QUANTUM EARBUDS -->
                    <article class="neo-card">
                        <div class="circular-neon-badge">TECH</div>
                        <div class="card-img-wrapper">
                            <img src="{{ asset('mockups/prop3_tablet.png') }}" alt="Quantum Earbuds" class="card-thumb-img" style="border-radius: 8px;">
                        </div>
                        <div class="card-body-info">
                            <div class="card-title-row">
                                <span class="card-product-title">QUANTUM EARBUDS</span>
                                <span class="rating-pill">★ 4.3</span>
                            </div>
                            <div class="card-meta-row">
                                <span class="card-price">225 000 FCFA</span>
                                <div class="color-swatches">
                                    <span class="c-dot cyan"></span>
                                    <span class="c-dot blue"></span>
                                    <span class="c-dot dark"></span>
                                </div>
                            </div>
                            <a href="{{ route('products') }}" class="btn-quick-buy">Quick Buy</a>
                        </div>
                    </article>

                    <!-- CARD 2: TITAN AI SMARTPHONE -->
                    <article class="neo-card">
                        <div class="circular-neon-badge">AI</div>
                        <div class="card-img-wrapper">
                            <img src="{{ asset('mockups/prop2_mobile.png') }}" alt="Titan AI Smartphone" class="card-thumb-img" style="border-radius: 8px;">
                        </div>
                        <div class="card-body-info">
                            <div class="card-title-row">
                                <span class="card-product-title">TITAN AI SMARTPHONE</span>
                                <span class="rating-pill">★ 4.8</span>
                            </div>
                            <div class="card-meta-row">
                                <span class="card-price">850 000 FCFA</span>
                                <div class="color-swatches">
                                    <span class="c-dot cyan"></span>
                                    <span class="c-dot dark"></span>
                                </div>
                            </div>
                            <a href="{{ route('products') }}" class="btn-quick-buy">Quick Buy</a>
                        </div>
                    </article>

                    <!-- CARD 3: AETHERA ARC SPEAKER -->
                    <article class="neo-card">
                        <div class="circular-neon-badge">PRO</div>
                        <div class="card-img-wrapper">
                            <img src="{{ asset('mockups/prop3_desktop.png') }}" alt="Aethera Arc Speaker" class="card-thumb-img" style="border-radius: 8px;">
                        </div>
                        <div class="card-body-info">
                            <div class="card-title-row">
                                <span class="card-product-title">AETHERA ARC SPEAKER</span>
                                <span class="rating-pill">★ 5.0</span>
                            </div>
                            <div class="card-meta-row">
                                <span class="card-price">490 000 FCFA</span>
                                <div class="color-swatches">
                                    <span class="c-dot cyan"></span>
                                    <span class="c-dot dark"></span>
                                </div>
                            </div>
                            <a href="{{ route('products') }}" class="btn-quick-buy">Quick Buy</a>
                        </div>
                    </article>

                    <!-- CARD 4: NEBULA WATCH -->
                    <article class="neo-card">
                        <div class="circular-neon-badge">OLED</div>
                        <div class="card-img-wrapper">
                            <img src="{{ asset('mockups/prop1_tablet.png') }}" alt="Nebula Watch" class="card-thumb-img" style="border-radius: 8px;">
                        </div>
                        <div class="card-body-info">
                            <div class="card-title-row">
                                <span class="card-product-title">NEBULA WATCH</span>
                                <span class="rating-pill">★ 4.7</span>
                            </div>
                            <div class="card-meta-row">
                                <span class="card-price">340 000 FCFA</span>
                                <div class="color-swatches">
                                    <span class="c-dot cyan"></span>
                                    <span class="c-dot blue"></span>
                                </div>
                            </div>
                            <a href="{{ route('products') }}" class="btn-quick-buy">Quick Buy</a>
                        </div>
                    </article>

                </div>
            </section>

            <!-- ============================================-->
            <!-- 3. LIVEWIRE DYNAMIC PRODUCTS CATALOG -->
            <!-- ============================================-->
            <section class="mb-6">
                <div class="section-heading-row">
                    <span class="section-label">Toutes les offres & Promotions</span>
                </div>
                <div class="swiper-theme-container products-slider mb-5">
                    <div class="swiper swiper theme-slider"
                        data-swiper='{
                            "slidesPerView":2,
                            "spaceBetween":16,
                            "autoplay": {"delay": 5000, "disableOnInteraction": false},
                            "breakpoints":{
                                "576":{"slidesPerView":2,"spaceBetween":16},
                                "768":{"slidesPerView":3,"spaceBetween":18},
                                "1200":{"slidesPerView":4,"spaceBetween":20}
                            }
                        }'>
                        <livewire:client.top-deals />
                    </div>
                </div>
            </section>

        </div>

        @include('partials.footer')

    </main>

    <style>
        /* Ambient Grid */
        .cyber-grid-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: 
                radial-gradient(circle at 50% 20%, rgba(56, 189, 248, 0.12) 0%, transparent 60%),
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 100% 100%, 60px 60px, 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* Hero */
        .hero-section {
            padding: 30px 0 40px;
        }

        .hero-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            align-items: center;
        }

        @media (min-width: 992px) {
            .hero-layout {
                grid-template-columns: 1.1fr 1fr;
                gap: 40px;
            }
        }

        .hero-headline {
            font-size: clamp(2rem, 4.5vw, 3.6rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 24px;
            color: #FFF;
        }

        .hero-headline .highlight {
            color: #00E5FF;
            text-shadow: 0 0 20px rgba(0, 229, 255, 0.6);
        }

        .hero-buttons-group {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-pill-explore {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 28px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 999px;
            color: #FFF;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-pill-explore:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: #FFF;
            color: #FFF;
        }

        .btn-pill-preorder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 28px;
            background: rgba(0, 229, 255, 0.1);
            border: 1.5px solid #00E5FF;
            border-radius: 999px;
            color: #00E5FF;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 0 20px rgba(0, 229, 255, 0.35);
            transition: all 0.3s ease;
        }

        .btn-pill-preorder:hover {
            background: #00E5FF;
            color: #0A0E17;
            box-shadow: 0 0 30px rgba(0, 229, 255, 0.7);
        }

        .hero-showcase-stage {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 280px;
        }

        .hologram-stage-glow {
            position: absolute;
            bottom: 20px;
            width: 240px;
            height: 50px;
            border-radius: 50%;
            background: radial-gradient(ellipse, rgba(0, 229, 255, 0.35) 0%, transparent 70%);
            box-shadow: 0 0 35px rgba(0, 229, 255, 0.4);
            border: 1px solid rgba(0, 229, 255, 0.3);
            pointer-events: none;
        }

        .hero-headphone-img {
            max-width: 80%;
            max-height: 260px;
            object-fit: contain;
            filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.8));
            position: relative;
            z-index: 2;
            animation: floatingHover 4s ease-in-out infinite alternate;
        }

        @keyframes floatingHover {
            from { transform: translateY(0); }
            to { transform: translateY(-10px); }
        }

        .slider-vertical-dots {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            display: none;
            flex-direction: column;
            gap: 10px;
        }

        @media (min-width: 1200px) {
            .slider-vertical-dots {
                display: flex;
            }
        }

        .v-dot {
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
        }

        .v-dot.active {
            height: 16px;
            border-radius: 4px;
            background: #00E5FF;
            box-shadow: 0 0 8px #00E5FF;
        }

        /* Product Cards */
        .section-heading-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-label {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #94A3B8;
        }

        .products-grid-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }

        @media (min-width: 992px) {
            .products-grid-container {
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }
        }

        .neo-card {
            background: rgba(18, 26, 43, 0.75);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(56, 189, 248, 0.22);
            border-radius: 20px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: all 0.3s ease;
        }

        .neo-card:hover {
            transform: translateY(-5px);
            border-color: #00E5FF;
            box-shadow: 0 12px 30px rgba(0, 229, 255, 0.2);
            background: rgba(24, 34, 56, 0.9);
        }

        .circular-neon-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1.5px solid #00F5A0;
            box-shadow: 0 0 10px rgba(0, 245, 160, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: 800;
            color: #00F5A0;
            text-transform: uppercase;
            z-index: 3;
        }

        .card-img-wrapper {
            height: 140px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .card-thumb-img {
            max-width: 75%;
            max-height: 75%;
            object-fit: contain;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.7));
            transition: transform 0.3s ease;
        }

        .neo-card:hover .card-thumb-img {
            transform: scale(1.08);
        }

        .card-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .card-product-title {
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #FFF;
        }

        .rating-pill {
            font-size: 11px;
            font-weight: 700;
            color: #FBBF24;
        }

        .card-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .card-price {
            font-size: 15px;
            font-weight: 800;
            color: #00F5A0;
            text-shadow: 0 0 8px rgba(0, 245, 160, 0.4);
        }

        .color-swatches {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .c-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .c-dot.cyan { background: #38BDF8; box-shadow: 0 0 4px #38BDF8; }
        .c-dot.blue { background: #3B82F6; }
        .c-dot.dark { background: #1E293B; border: 1px solid #475569; }

        .btn-quick-buy {
            width: 100%;
            padding: 8px 0;
            background: transparent;
            border: 1.5px solid #00E5FF;
            border-radius: 999px;
            color: #00E5FF;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0, 229, 255, 0.2);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-quick-buy:hover {
            background: #00E5FF;
            color: #0A0E17;
            box-shadow: 0 0 18px rgba(0, 229, 255, 0.6);
        }
    </style>
@endsection
