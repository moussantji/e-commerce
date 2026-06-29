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
    <link href="{{ asset('assets/css/theme.min.css') }}" type="text/css" rel="stylesheet">

    <style>


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



    @stack('scripts')

    <script>
        const loader = document.querySelector(".page-loader");
        if (!loader) {
            document.body.classList.add("fade-ready");
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("animate");
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        const header = document.querySelector(".d-flex.flex-between-center.mb-3");
        if (header) observer.observe(header);

        const slider = document.querySelector(".swiper-theme-container.products-slider");
        if (slider) observer.observe(slider);

        document.querySelectorAll("[data-hidden]").forEach(el => observer.observe(el));
        document.querySelectorAll(".top-deals-card").forEach(el => observer.observe(el));
    </script>



</body>


</html>
