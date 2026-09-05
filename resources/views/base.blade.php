<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" data-navigation-type="default"
    data-navbar-horizontal-shape="default">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>@yield('title')</title>

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/img/favicons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicons/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicons/mstile-150x150.png') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Scripts -->
    <script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">
    <link href="{{ asset('vendors/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <style>
        /* ANIMATION PAGE COMPLÈTE */
        /* LOADING SCREEN PRO */
        /* LOADER PRO */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            /* Transition EXACTE 0.8s */
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .page-loader.fade-out {
            opacity: 0;
            transform: scale(1.05);
            visibility: hidden;
        }

        .loader-circle {
            width: 80px;
            height: 80px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top: 4px solid #fff;
            border-radius: 50%;
            animation: spinPro 1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
            position: relative;
        }

        .loader-glow {
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            margin: auto;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulseGlow 2s ease-out infinite;
        }

        @keyframes spinPro {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulseGlow {
            0% {
                opacity: 1;
                transform: scale(0.8);
            }

            100% {
                opacity: 0;
                transform: scale(1.2);
            }
        }
    </style>

    <style>
        /* ANIMATION SECTION AU SCROLL */

        /* banniere full width */
        /* Bannières invisibles au début */
        .whooping-banner,
        .gift-items-banner,
        .best-in-market-banner {
            opacity: 0;
        }

        /* Délai + fade après loader */
        .whooping-banner.fade-ready {
            animation: fadeInBanner 1s ease-out 0.2s forwards;
        }

        .gift-items-banner.fade-ready {
            animation: fadeInBanner 1s ease-out 0.5s forwards;
        }

        .best-in-market-banner.fade-ready {
            animation: fadeInBanner 1s ease-out 0.8s forwards;
        }

        @keyframes fadeInBanner {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animation d'entrée de la section entière */
        /* Top Deals Header */
        .d-flex.flex-between-center.mb-3 {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .d-flex.flex-between-center.mb-3.animate {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        /* Bolts icons */
        .d-flex.flex-between-center .fas.fa-bolt {
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.5s ease;
        }

        .d-flex.flex-between-center.animate .fas.fa-bolt {
            opacity: 1 !important;
            transform: scale(1) !important;
        }

        /* Swiper container */
        .swiper-theme-container.products-slider {
            opacity: 0;
            transform: translateX(-50px) scale(0.95);
            transition: all 0.9s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .swiper-theme-container.products-slider.animate {
            opacity: 1 !important;
            transform: translateX(0) scale(1) !important;
        }

        /* Sidebar image */
        .col-lg-3 .h-100 {
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.8s ease;
        }

        .col-lg-3 .h-100.animate {
            opacity: 1 !important;
            transform: scale(1) !important;
        }

        /* Mobile image */
        .col-12.d-lg-none img {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .col-12.d-lg-none img.animate {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        /* SUPPRIME opacity:0 initial → utilise data-hidden */
        [data-hidden] {
            opacity: 0;
            transform: translateY(60px);
            transition: all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        [data-hidden].animate {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        /* Animation SEULEMENT sur les product-card (PAS swiper-slide) */
        .top-deals-card {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .top-deals-card.animate {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    </style>
    <style>
        /* Garde animation d'entrée */
        .scrollbar {
            opacity: 0;
            transform: translateY(50px);
            animation: slideInUpPro 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }

        .scrollbar-nav {
            overflow: hidden;
            /* ✅ GARDÉ pour navbar */
            white-space: nowrap;
        }

        @keyframes slideInUpPro {
            0% {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }

            50% {
                opacity: 0.7;
                transform: translateY(15px) scale(0.98);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Les icônes arrivent une par une */
        .scroll-content .icon-nav-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .scroll-content .icon-nav-item:nth-child(2) {
            animation-delay: 0.15s;
        }

        .scroll-content .icon-nav-item:nth-child(3) {
            animation-delay: 0.2s;
        }

        .scroll-content .icon-nav-item:nth-child(4) {
            animation-delay: 0.25s;
        }

        .scroll-content .icon-nav-item:nth-child(5) {
            animation-delay: 0.3s;
        }

        .scroll-content .icon-nav-item:nth-child(6) {
            animation-delay: 0.35s;
        }

        .scroll-content .icon-nav-item:nth-child(7) {
            animation-delay: 0.4s;
        }

        .scroll-content .icon-nav-item:nth-child(8) {
            animation-delay: 0.45s;
        }

        .scroll-content .icon-nav-item:nth-child(9) {
            animation-delay: 0.5s;
        }

        .scroll-content .icon-nav-item:nth-child(10) {
            animation-delay: 0.55s;
        }

        .scroll-content .icon-nav-item:nth-child(11) {
            animation-delay: 0.6s;
        }

        .icon-nav-item {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUpItem 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }

        @keyframes fadeInUpItem {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scroll-track {
            display: flex !important;
            animation: scrollInfinite 35s linear infinite;
            width: max-content;
        }

        .scroll-content {
            display: flex;
            gap: 30px;
            flex-shrink: 0;
            padding-right: 20px;
        }

        .icon-nav-item {
            text-decoration: none;
            color: inherit;
            flex-shrink: 0;
            min-width: 100px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .icon-container {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            transition: all 0.3s ease;
        }

        .nav-label {
            font-size: 0.85rem;
            font-weight: 600;
            margin: 0;
        }

        @keyframes scrollInfinite {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* Pause au hover */
        .scrollbar:hover .scroll-track {
            animation-play-state: paused !important;
        }

        /* Effets hover */
        /* Effets hover SANS FOND BLANC */
        .icon-nav-item:hover {
            transform: scale(1.2) translateY(-10px);
        }

        .icon-nav-item:hover .icon-container {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            /* ✅ SUPPRIMÉ: background: rgba(255,255,255,0.95) !important; */
            border: 2px solid rgba(255, 255, 255, 0.4);
            /* Bordure subtile à la place */
        }

        .icon-nav-item:hover .nav-label {
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            color: inherit !important;
            /* Garde la couleur originale */
        }



        /* Toast minimaliste très visible */
        #toastNotification {
            position: fixed !important;
            top: 20px !important;
            right: 20px !important;
            max-width: 340px !important;
            padding: 12px 16px !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
            z-index: 10000 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 14px !important;
            font-family: Arial, sans-serif !important;
            background: #fff !important;
            border: 2px solid #000 !important;
            color: #000 !important;
        }

        /* Ne pas animer tout de suite, pour vérifier qu'il est bien là */
        .toast-icon {
            font-size: 18px !important;
        }

        .toast-message {
            flex: 1 !important;
        }

        /* Désactive l'animation au début */
        .toast.showing {
            transform: translateX(0) !important;
        }


        .toast.toast-success {
            background: #f0fdf4;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .rating-stars {
            letter-spacing: 8px;
            cursor: pointer;
        }

        .rating-stars span {
            color: #aaa;
        }

        .rating-stars span.selected {
            color: #f39c12;
        }



        .toast.toast-error {
            background: #fdf2f2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            width: 100%;
            background: currentColor;
            opacity: 0.3;
            border-radius: 0 0 8px 8px;
        }
    </style>

    <style>
        /* TOUTES les pages : fade in après loader.
           ⚠️ On N'inclut PAS body ici : un transform sur body casserait
           le position:fixed de la barre de navigation mobile. */
        main,
        .container,
        .produit-main,
        .content-wrapper {
            opacity: 0;
            animation: fadeInGlobal 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }

        @keyframes fadeInGlobal {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 576px) {
            .order-items-scroll {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding: 10px 0;
                scrollbar-width: thin;
            }

            /* ✅ CORRIGÉ : utilise flex au lieu d'inline-table */
            .order-items-scroll>.border-dashed>.ms-n2 {
                min-width: 420px;
                /* Force scroll horizontal */
                display: flex !important;
                flex-direction: column;
                gap: 12px;
            }

            /* Scrollbar discrète mais fluide */
            .order-items-scroll::-webkit-scrollbar {
                height: 4px;
            }

            .order-items-scroll::-webkit-scrollbar-track {
                background: transparent;
            }

            .order-items-scroll::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.4);
                border-radius: 2px;
            }
        }
    </style>

    <style>
        /*forcer le centrage vertical et horizontal du modal*/
        .modal.fade .modal-dialog {
            transform: none !important;
            margin: auto !important;
        }

        .modal-dialog-centered {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100% - 1rem);
        }

        /*Sur mobile : garder un peu de marge sur les côtés*/
        @media (max-width: 576px) {
            .modal-dialog.modal-lg {
                max-width: 95% !important;
                margin: 0 auto !important;
            }
        }

        /*Fond sombre semi-transparent*/
        .modal-backdrop {
            opacity: 0.5 !important;
        }
    </style>
    <script>
        var phoenixIsRTL = window.config.config.phoenixIsRTL;
        if (phoenixIsRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>


    @yield('content')

    @includeIf('partials.bottom-nav')

    @if (session('success') || session('error'))
        <div id="toastNotification" class="toast {{ session('error') ? 'toast-error' : 'toast-success' }}">
            <div class="toast-icon">
                {{ session('error') ? '⚠️' : '✅' }}
            </div>
            <div class="toast-message">
                {{ session('error') ?? session('success') }}
            </div>
            <div class="toast-progress"></div>
        </div>
    @endif




    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->

    <script>
        document.querySelectorAll('.js-qty-minus, .js-qty-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                // ✅ ID bouton = "qty-plus-10" → extrait "10"
                const productId = this.id.match(/qty-(minus|plus)-(\d+)/)?.[2];

                const input = this.closest('[data-quantity="data-quantity"]').querySelector('.qty-input');
                const hiddenInput = document.getElementById(`qty-hidden-${productId}`);

                let qty = parseInt(input.value) || 1;
                if (this.dataset.action === 'decrement') {
                    qty = Math.max(1, qty - 1);
                } else {
                    qty = Math.min(99, qty + 1);
                }

                input.value = qty;
                hiddenInput.value = qty;
            });
        });
    </script>


    <script src="{{ asset('vendors/dropzone/dropzone-min.js') }}"></script>
    <script src="{{ asset('vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/js/phoenix.js') }}"></script>
    <script src="{{ asset('vendors/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards/ecommerce-dashboard.js') }}"></script>
    <script src="{{ asset('vendors/rater-js/index.js') }}"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('toastNotification');
            if (!toast) return;

            const progress = toast.querySelector('.toast-progress');
            progress.style.width = '100%';
            progress.style.transition = 'width 10s linear';

            // Lancer le décompte de la barre de progression
            setTimeout(() => {
                progress.style.width = '0';
            }, 50);

            // Fermer le toast après 10s
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(110%)';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }, 10000);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. Carrousel de catégories : clone le contenu pour un défilement infini ---
            const original = document.getElementById('scrollContent');
            const clone = document.getElementById('scrollContentClone');
            const scrollTrack = document.querySelector('.scroll-track');
            if (original && clone) {
                clone.innerHTML = original.innerHTML;
            }

            // --- 2. Observer de révélation au scroll (réutilisable, robuste) ---
            const revealSelector = '[data-hidden], .top-deals-card, .d-flex.flex-between-center.mb-3, .swiper-theme-container.products-slider, .col-lg-3 .h-100, .col-12.d-lg-none img';

            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.15,
                rootMargin: '0px 0px -80px 0px'
            });

            const revealAll = () => {
                document.querySelectorAll(revealSelector).forEach(el => revealObserver.observe(el));
            };
            revealAll();

            // Filet de sécurité : si quelque chose empêche l'observer de se déclencher,
            // on force l'affichage pour que le contenu ne reste jamais invisible.
            const forceVisible = () => {
                document.querySelectorAll(revealSelector).forEach(el => el.classList.add('animate'));
                document.querySelectorAll('.whooping-banner, .gift-items-banner, .best-in-market-banner')
                    .forEach(el => el.classList.add('fade-ready'));
            };

            // --- 3. Loader + démarrage des animations ---
            const startPage = () => {
                document.body.classList.add('fade-ready');
                if (scrollTrack) {
                    scrollTrack.style.animationPlayState = 'running';
                }
                document.querySelectorAll('.whooping-banner, .gift-items-banner, .best-in-market-banner')
                    .forEach(el => el.classList.add('fade-ready'));
                // Re-scanne le DOM (utile après le rendu des composants Livewire)
                revealAll();
            };

            const loader = document.getElementById('pageLoader');
            if (loader) {
                // Met le carrousel en pause tant que le loader est visible
                if (scrollTrack) scrollTrack.style.animationPlayState = 'paused';
                const hideLoader = () => {
                    loader.classList.add('fade-out');
                    setTimeout(startPage, 600);
                };
                if (document.readyState === 'complete') {
                    setTimeout(hideLoader, 800);
                } else {
                    let done = false;
                    const run = () => { if (!done) { done = true; hideLoader(); } };
                    window.addEventListener('load', () => setTimeout(run, 400));
                    setTimeout(run, 2500); // garde-fou si l'évènement load tarde
                }
            } else {
                startPage();
            }

            // Filet de sécurité global : tout est visible au plus tard après 4s
            setTimeout(forceVisible, 4000);
        });
    </script>





    @livewireScripts

    {{-- 🔥 FIX FEATHER + LIVEWIRE --}}
    <script>
        document.addEventListener('livewire:init', () => {
            // À CHAQUE update Livewire → re-init Feather
            Livewire.hook('morph.updated', () => {
                setTimeout(() => {
                    if (typeof feather !== 'undefined') {
                        feather.replace({
                            'stroke-width': 2,
                            width: '20',
                            height: '20'
                        });
                    }
                }, 100);
            });
        });
    </script>



    @include('partials.mobile-bottom-nav')

    @stack('scripts')


</body>


</html>
