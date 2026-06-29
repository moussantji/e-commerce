@extends('base')

@section('title', 'Tableau de bord')

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
                            <a href="{{ route('home') }}">
                                <i class="fas fa-home me-1"></i>
                                {{ __('Accueil') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            {{ __('Tableau de bord') }}
                        </li>
                    </ol>
                </nav>
                <div class="row align-items-center justify-content-between g-3 mb-4">
                    <div class="col-auto">
                        <h2 class="mb-0">{{ __('Tableau de bord') }}</h2>
                    </div>
                    <div class="col-auto">
                        <div class="row g-2 g-sm-3">
                            <div class="col-auto"><button class="btn btn-phoenix-secondary"
                                    onclick="document.querySelector('a[href=\'#tab-password\']').click();
                 document.getElementById('tab-password').scrollIntoView({behavior: 'smooth'})"><span
                                        class="fas fa-key me-2"></span>{{ __('Réinitialiser le mot de passe') }}</button></div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-6">
                    <div class="col-12 col-lg-8">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="border-bottom border-dashed pb-4">
                                    <div class="row align-items-center g-3 g-sm-5 text-center text-sm-start">
                                        <livewire:user-avatar :user="$user" />
                                        <div class="col-12 col-sm-auto flex-1">
                                            <h3>{{ $user->name }}</h3>
                                            @php
                                                $date = $user?->created_at ?? now();
                                                $days = abs(floor(now()->diffInDays($date)));
                                                $months = abs(floor(now()->diffInMonths($date)));
                                            @endphp

                                            <p class="text-body-secondary">
                                                Rejoint il y a
                                                @if ($days < 30)
                                                    {{ $days }} {{ $days == 1 ? 'jour' : 'jours' }}
                                                @elseif($months < 12)
                                                    {{ $months }} {{ $months == 1 ? 'mois' : 'mois' }}
                                                @else
                                                    {{ abs(floor(now()->diffInYears($date))) }}
                                                    {{ abs(floor(now()->diffInYears($date))) > 1 ? 'ans' : 'an' }}
                                                @endif
                                            </p>



                                            <div><a class="me-2" href="#!"><span
                                                        class="fab fa-linkedin-in text-body-quaternary text-opacity-75 text-primary-hover"></span></a><a
                                                    class="me-2" href="#!"><span
                                                        class="fab fa-facebook text-body-quaternary text-opacity-75 text-primary-hover"></span></a><a
                                                    href="#!"><span
                                                        class="fab fa-twitter text-body-quaternary text-opacity-75 text-primary-hover"></span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-between-center pt-4">
                                    <div>
                                        <h6 class="mb-2 text-body-secondary">{{ __('Total dépensé') }}</h6>
                                        <h4 class="fs-7 text-body-highlight mb-0">
                                            {{ number_format($user->orders()->sum('total'), 0) }} FCFA</h4>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="mb-2 text-body-secondary">{{ __('Dernière commande') }}</h6>
                                        <h4 class="fs-7 text-body-highlight mb-0">
                                            @php
                                                $lastOrder = $user->orders()->latest()->first();
                                            @endphp

                                            @if ($lastOrder)
                                                @php
                                                    $date = $lastOrder->created_at;
                                                    $days = abs(floor(now()->diffInDays($date)));
                                                    $months = abs(floor(now()->diffInMonths($date)));
                                                    $years = abs(floor(now()->diffInYears($date)));
                                                @endphp

                                                @if ($days < 30)
                                                    il y a {{ $days }} {{ $days == 1 ? 'jour' : 'jours' }}
                                                @elseif($months < 12)
                                                    il y a {{ $months }} {{ $months == 1 ? 'mois' : 'mois' }}
                                                @else
                                                    il y a {{ $years }} {{ $years > 1 ? 'ans' : 'an' }}
                                                @endif
                                            @else
                                                <span class="text-muted">Aucune commande</span>
                                            @endif

                                        </h4>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="mb-2 text-body-secondary">{{ __('Nombre total de commandes') }}</h6>
                                        <h4 class="fs-7 text-body-highlight mb-0">{{ $user->orders()->count() }} </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="border-bottom border-dashed">
                                    <h4 class="mb-3">{{ __('Adresse par défaut') }}<button class="btn btn-link p-0"
                                            onclick="document.querySelector('a[href=\'#tab-personal-info\']').click();
                                        document.getElementById('tab-personal-info').scrollIntoView({behavior: 'smooth'})"
                                            type="button">
                                            <span class="fas fa-edit fs-9 ms-3 text-body-quaternary"></span></button></h4>
                                </div>
                                <div class="pt-4 mb-7 mb-lg-4 mb-xl-7">
                                    <div class="row justify-content-between">
                                        <div class="col-auto">
                                            <h5 class="text-body-highlight">{{ __('Adresse') }}</h5>
                                        </div>
                                        <div class="col-auto">
                                            <p class="text-body-secondary">
                                                {{ $user->adresse['adresse'] ?? 'Aucune adresse' }}<br />
                                                {{ $adresseData['pays'] ?? 'Pays inconnu' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-top border-dashed pt-4">
                                    <div class="row flex-between-center mb-2">
                                        <div class="col-auto">
                                            <h5 class="text-body-highlight mb-0">Courriel</h5>
                                        </div>
                                        <div class="col-auto"><a class="lh-1"
                                                href="mailto:{{ $user->email }}">{{ $user->email }}</a></div>
                                    </div>
                                    <div class="row flex-between-center">
                                        <div class="col-auto">
                                            <h5 class="text-body-highlight mb-0">Téléphone</h5>
                                        </div>
                                        <div class="col-auto"><a
                                                href="tel:{{ $user->tel }}">{{ $user->tel ?? 'Aucun' }}</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                @include('partials.dashboard')
            </div><!-- end of .container-->
        </section><!-- <section> close ============================-->
        <!-- ============================================-->

        <!-- Search Modal -->
        @include('admin.partials.search_modal')

        @include('partials.footer')

    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->


    <script>
        document.getElementById('avatarFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>

@endsection
