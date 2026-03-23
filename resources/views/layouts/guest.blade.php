{{-- resources/views/layouts/phoenix.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" data-navigation-type="default"
    data-navbar-horizontal-shape="default">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title dynamique -->
    <title>{{ $title ?? config('app.name', 'Phoenix') }}</title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicons/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicons/mstile-150x150.png') }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Stylesheets -->
    <link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

    {{-- RTL/LTR conditionnel --}}
    <link href="{{ asset('assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl" disabled>
    <link href="{{ asset('assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">
    <link href="{{ asset('assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl"
        disabled>

    <style>
        /* TOUTES les pages : fade in après loader */
        body.fade-ready,
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
    </style>

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
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>
    {{ $slot }}

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

    <!-- Vendors JS -->
    <script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('vendors/dayjs/dayjs.min.js') }}"></script>

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

            setTimeout(() => {
                const loader = document.getElementById('pageLoader');
                if (loader) {
                    loader.classList.add('fade-out');

                    setTimeout(() => {
                        // ✅ FADE IN TOUTES LES PAGES
                        document.body.classList.add('fade-ready');

                        // Scrollbar seulement si existe
                        const scrollTrack = document.querySelector('.scroll-track');
                        if (scrollTrack) {
                            scrollTrack.style.animationPlayState = 'running';
                        }
                    }, 850);
                }
            }, 2500);
        });
    </script>

    <!-- Phoenix Theme -->
    <script src="{{ asset('assets/js/phoenix.js') }}"></script>

    {{-- RTL/LTR script --}}
    <script>
        var phoenixIsRTL = window.config?.config?.phoenixIsRTL || false;
        if (phoenixIsRTL) {
            document.getElementById('style-default').disabled = true;
            document.getElementById('user-style-default').disabled = true;
            document.getElementById('style-rtl').disabled = false;
            document.getElementById('user-style-rtl').disabled = false;
            document.documentElement.setAttribute('dir', 'rtl');
        }
    </script>

    @stack('scripts')
</body>

</html>



