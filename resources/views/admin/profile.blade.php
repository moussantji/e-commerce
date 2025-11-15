@extends('admin.base')

@section('title', 'Mon Profil')


@section('content')



    <!-- ============================================-->
    <!-- <section> begin ============================-->
    <div class="content">
        <div class="container-small">
            <div class="row align-items-center justify-content-between g-3 mb-4">
                <div class="col-auto">
                    <h2 class="mb-0">Profile</h2>
                </div>
                <div class="col-auto">
                    <div class="row g-2 g-sm-3">
                        <div class="col-auto">
                            <button type="button" class="btn btn-phoenix-danger">
                                <span class="fas fa-trash-alt me-2"></span>Supprimer le compte
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-phoenix-secondary"
                                onclick="document.querySelector('a[href=\'#tab-password\']').click(); 
                 document.getElementById('tab-password').scrollIntoView({behavior: 'smooth'})">
                                <span class="fas fa-key me-2"></span>Réinitialiser le mot de passe
                            </button>
                        </div>
                    </div>
                </div>
                @if (session('success'))
                    <div class="alert alert-success mt-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mt-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="row g-3 mb-6">
                <div class="col-12 col-lg-8">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="border-bottom border-dashed pb-4">
                                <div class="row align-items-center g-3 g-sm-5 text-center text-sm-start">
                                    <div class="col-12 col-sm-auto">
                                        <div class="col-12 col-sm-auto"><input class="d-none" id="avatarFile"
                                                type="file" /><label class="cursor-pointer avatar avatar-5xl"
                                                for="avatarFile"><img class="rounded-circle"
                                                    src="../../../assets/img/team/15.webp" alt="" /></label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-auto flex-1">
                                        <h3>{{ $user->name }}</h3>
                                        <p class="text-body-secondary">Membre depuis
                                            @php
                                                $now = \Carbon\Carbon::now();
                                                $created = \Carbon\Carbon::parse($user->created_at);
                                                if ($created->isToday()) {
                                                    echo round($created->diffInMinutes($now) / 60) . ' heures';
                                                } else {
                                                    echo str_replace(',', '', $created->diffForHumans(['parts' => 2, 'join' => ' et ', 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]));
                                                }
                                            @endphp
                                        </p>
                                        <div class="text-body-secondary">
                                            <span class="me-3"><i class="fas fa-envelope me-1"></i>
                                                {{ $user->email }}</span>
                                            @if ($user->phone)
                                                <span><i class="fas fa-phone me-1"></i> {{ $user->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-between-center pt-4">
                                <div>
                                    <h6 class="mb-2 text-body-secondary">Date d'inscription</h6>
                                    <h4 class="fs-7 text-body-highlight mb-0">{{ $user->created_at->translatedFormat('j F Y') }}</h4>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-2 text-body-secondary">Dernière connexion</h6>
                                    <h4 class="fs-7 text-body-highlight mb-0">
                                        @if($user->last_login)
                                            @php
                                                $lastLogin = \Carbon\Carbon::parse($user->last_login);
                                                if ($lastLogin->isToday()) {
                                                    echo round($lastLogin->diffInMinutes(now()) / 60) . ' heures';
                                                } else {
                                                    echo str_replace(',', '', $lastLogin->diffForHumans(['parts' => 2, 'join' => ' et ', 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]));
                                                }
                                            @endphp
                                        @else
                                            Jamais
                                        @endif
                                    </h4>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-2 text-body-secondary">Statut</h6>
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="border-bottom border-dashed d-flex justify-content-between align-items-center">
                                <h4 class="mb-3">Adresse par défaut</h4>
                                <button type="button"
                                    onclick="document.querySelector('a[href=\'#tab-personal-info\']').click(); 
                                        document.getElementById('tab-personal-info').scrollIntoView({behavior: 'smooth'})"
                                    class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#editAddressModal">
                                    <span class="fas fa-edit fs-9 text-body-quaternary"></span>
                                </button>
                            </div>
                            <div class="pt-4 mb-7 mb-lg-4 mb-xl-7">
                                <div class="row justify-content-between">
                                    <div class="col-auto">
                                        <h5 class="text-body-highlight">Address</h5>
                                    </div>
                                    <div class="col-auto">
                                        <p class="text-body-secondary">
                                            @if (!empty($user->address))
                                                {{ $user->address }}
                                            @else
                                                Aucune adresse enregistrée
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="border-top border-dashed pt-4">
                                <div class="row flex-between-center mb-2">
                                    <div class="col-auto">
                                        <h5 class="text-body-highlight mb-0">Email</h5>
                                    </div>
                                    <div class="col-auto">
                                        <a class="lh-1" href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    </div>
                                </div>
                                @if ($user->phone)
                                    <div class="row flex-between-center">
                                        <div class="col-auto">
                                            <h5 class="text-body-highlight mb-0">Téléphone</h5>
                                        </div>
                                        <div class="col-auto">
                                            <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="scrollbar">
                    <ul class="nav nav-underline fs-9 flex-nowrap mb-3 pb-1" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link text-nowrap active" id="personal-info-tab" data-bs-toggle="tab"
                                href="#tab-personal-info" role="tab" aria-controls="tab-personal-info"
                                aria-selected="true">
                                <span class="fas fa-user me-2"></span>Informations personnelles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-nowrap" id="password-tab" data-bs-toggle="tab" href="#tab-password"
                                role="tab" aria-controls="tab-password" aria-selected="false">
                                <span class="fas fa-key me-2"></span>Mot de passe
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="profileTabContent">
                    <div class="tab-pane fade show active" id="tab-personal-info" role="tabpanel"
                        aria-labelledby="personal-info-tab">
                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row gx-3 gy-4 mb-5">
                                <div class="col-12 col-lg-6">
                                    <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                        for="nom">Nom</label>
                                    <input class="form-control" id="nom" name="name" type="text"
                                        value="{{ $user->name }}" />
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                        for="prenom">Prénom</label>
                                    <input class="form-control" id="prenom" name="prenom" type="text"
                                        value="{{ $user->prenom }}" />
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                        for="date_naiss">Date de naissance</label>
                                    <input type="date" class="form-control" id="date_naiss" name="date_naiss"
                                        value="{{ $user->date_naiss ? (is_string($user->date_naiss) ? \Carbon\Carbon::parse($user->date_naiss)->format('Y-m-d') : $user->date_naiss->format('Y-m-d')) : '' }}">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                        for="lieu_naiss">Lieu de naissance</label>
                                    <input type="text" class="form-control" id="lieu_naiss" name="lieu_naiss"
                                        value="{{ $user->lieu_naiss }}">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                        for="email">Email</label>
                                    <input class="form-control" id="email" name="email" type="email"
                                        value="{{ $user->email }}" readonly />
                                    <small class="text-muted">Contactez l'administrateur pour modifier cette information</small>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                        for="tel">Téléphone</label>
                                    <input class="form-control" id="tel" name="tel" type="tel"
                                        value="{{ $user->tel }}" />
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                        for="pays">Pays</label>
                                    <input class="form-control" id="pays" name="pays" type="text"
                                        value="{{ $user->pays }}" />
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                        for="region">Région</label>
                                    <input class="form-control" id="region" name="region" type="text"
                                        value="{{ $user->region }}" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                        for="address">Adresse complète</label>
                                    <textarea class="form-control" id="adresse" name="adresse" rows="2">{{ $user->adresse ? (is_string($user->adresse) ? json_decode($user->adresse, true)['adresse'] ?? $user->adresse : $user->adresse) : '' }}</textarea>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-light"><i
                                                class="fab fa-facebook-f text-primary"></i></span>
                                        <input type="url" class="form-control" id="facebook_url" name="facebook_url"
                                            placeholder="https://facebook.com/votrepseudo"
                                            value="{{ $user->social_links ? (is_string($user->social_links) ? json_decode($user->social_links, true)['facebook'] ?? '' : ($user->social_links['facebook'] ?? '')) : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-light"><i
                                                class="fab fa-twitter text-info"></i></span>
                                        <input type="url" class="form-control" id="twitter_url" name="twitter_url"
                                            placeholder="https://twitter.com/votrepseudo"
                                            value="{{ $user->social_links ? (is_string($user->social_links) ? json_decode($user->social_links, true)['twitter'] ?? '' : ($user->social_links['twitter'] ?? '')) : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-light"
                                            style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: white;">
                                            <i class="fab fa-instagram"></i>
                                        </span>
                                        <input type="url" class="form-control" id="instagram_url"
                                            name="instagram_url" placeholder="https://instagram.com/votrepseudo"
                                            value="{{ $user->social_links ? (is_string($user->social_links) ? json_decode($user->social_links, true)['instagram'] ?? '' : ($user->social_links['instagram'] ?? '')) : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-light"><i
                                                class="fab fa-linkedin-in text-primary"></i></span>
                                        <input type="url" class="form-control" id="linkedin_url" name="linkedin_url"
                                            placeholder="https://linkedin.com/in/votrepseudo"
                                            value="{{ $user->social_links ? (is_string($user->social_links) ? json_decode($user->social_links, true)['linkedin'] ?? '' : ($user->social_links['linkedin'] ?? '')) : '' }}">
                                    </div>
                                </div>

                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary px-7">
                                    <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- Onglet de modification du mot de passe -->
                    <div class="tab-pane fade" id="tab-password" role="tabpanel" aria-labelledby="password-tab">
                        <form action="{{ route('admin.profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row gx-3 gy-4 mb-5">

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                            for="new_password">Nouveau mot de passe</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" required minlength="8">
                                            <button class="btn btn-outline-secondary password-toggle" type="button"
                                                onclick="const icon = this.firstElementChild;
                                                         const input = this.previousElementSibling;
                                                         if (input.type === 'password') {
                                                             icon.classList.remove('fa-eye');
                                                             icon.classList.add('fa-eye-slash');
                                                             input.type = 'text';
                                                         } else {
                                                             icon.classList.remove('fa-eye-slash');
                                                             icon.classList.add('fa-eye');
                                                             input.type = 'password';
                                                         }">
                                                <i class="far fa-eye"></i>
                                            </button>
                                        </div>
                                        <small class="text-body-tertiary">Minimum 8 caractères</small>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                            for="new_password_confirmation">Confirmer le mot de passe</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password_confirmation"
                                                name="new_password_confirmation" required>
                                            <button class="btn btn-outline-secondary password-toggle" type="button"
                                                onclick="const icon = this.firstElementChild;
                                                         const input = this.previousElementSibling;
                                                         if (input.type === 'password') {
                                                             icon.classList.remove('fa-eye');
                                                             icon.classList.add('fa-eye-slash');
                                                             input.type = 'text';
                                                         } else {
                                                             icon.classList.remove('fa-eye-slash');
                                                             icon.classList.add('fa-eye');
                                                             input.type = 'password';
                                                         }">
                                                <i class="far fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-7">
                                    <i class="fas fa-save me-2"></i>Mettre à jour le mot de passe
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
                <div class="mt-5">
                    @include('admin.partials.footer')
                </div>
            </div>

        </div><!-- end of .container-->

    </div><!-- <section> close ============================-->
    <!-- ============================================-->


@endsection
