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
            <div class="container-small">
                <nav class="mb-3" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Tableau de bord</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Détails de la commande
                        </li>
                    </ol>
                </nav>
                <h2 class="mb-5">Détails de la commande</h2>

                {{-- Suivi du statut de la commande --}}
                @php
                    $flow = ['en_attente', 'paiement_declare', 'payee', 'traitement', 'expedie', 'livre'];
                    $stepLabels = [
                        'en_attente' => 'En attente de paiement',
                        'paiement_declare' => 'Paiement en vérification',
                        'payee' => 'Payée',
                        'traitement' => 'En préparation',
                        'expedie' => 'Expédiée',
                        'livre' => 'Livrée',
                    ];
                    $normalized = [
                        'expedition' => 'expedie',
                        'livree' => 'livre',
                    ][$commande->statut] ?? $commande->statut;
                    $isCancelled = in_array($commande->statut, ['annule', 'annulee']);
                    $currentIndex = array_search($normalized, $flow);
                    if ($currentIndex === false) { $currentIndex = 0; }
                @endphp

                @if ($isCancelled)
                    <div class="alert alert-danger d-flex align-items-center mb-5">
                        <span class="fas fa-times-circle me-2"></span>
                        Cette commande a été annulée.
                    </div>
                @else
                    <div class="card mb-5">
                        <div class="card-body">
                            <div class="d-flex justify-content-between text-center flex-wrap gap-2">
                                @foreach ($flow as $i => $st)
                                    <div class="flex-fill" style="min-width:90px;">
                                        <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle {{ $i <= $currentIndex ? 'bg-primary text-white' : 'bg-body-secondary text-body-tertiary' }}"
                                            style="width:36px;height:36px;font-weight:700;">
                                            @if ($i < $currentIndex)
                                                <span class="fas fa-check"></span>
                                            @else
                                                {{ $i + 1 }}
                                            @endif
                                        </div>
                                        <small class="{{ $i <= $currentIndex ? 'fw-bold text-body' : 'text-body-tertiary' }}">
                                            {{ $stepLabels[$st] }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row justify-content-between">
                    <div class="col-lg-5 col-xl-4">
                        <div class="card mt-3 mt-lg-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h3 class="mb-0">Résumé</h3>
                                </div>
                                <livewire:frontend.checkout.cart-items :commande="$commande" />

                                {{-- Résumé des totaux --}}
                                <livewire:frontend.checkout.checkout-summary :commande="$commande" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-xl-7">
                        <div class="d-flex align-items-end">
                            <h3 class="mb-0 me-3">Informations de livraison</h3>
                        </div>
                        <table class="table table-borderless mt-4">
                            <tbody>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            <span class="fs-3 me-2" data-feather="user"
                                                style="
                                                                                            height: 16px;
                                                                                            width: 16px;
                                                                                        ">
                                            </span>
                                            <h5 class="lh-sm me-4">
                                                Nom
                                            </h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">
                                        :
                                    </td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-sm fw-normal text-body-secondary">
                                            {{ $user->full_name }}
                                        </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            <span class="fs-3 me-2" data-feather="home"
                                                style="
                                                                                            height: 16px;
                                                                                            width: 16px;
                                                                                        ">
                                            </span>
                                            <h5 class="lh-sm me-4">
                                                Adresse
                                            </h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">
                                        :
                                    </td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-lg fw-normal text-body-secondary">
                                            {{ $user->adresse ?? 'Bamako' }}
                                        </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            <span class="fs-3 me-2" data-feather="phone"
                                                style="
                                                                                            height: 16px;
                                                                                            width: 16px;
                                                                                        ">
                                            </span>
                                            <h5 class="lh-sm me-4">
                                                Téléphone
                                            </h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">
                                        :
                                    </td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-sm fw-normal text-body-secondary">
                                            {{ $user->tel ?? '+223 XXXXXXXX' }}
                                        </h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr class="my-6" />
                        <h3>Détails de facturation</h3>
                        <div class="form-check">
                            <input class="form-check-input" id="sameAsShipping" type="checkbox" checked="checked"
                                disabled /><label class="form-check-label fs-8 fw-normal" for="sameAsShipping">Même adresse
                                que pour la livraison
                            </label>
                        </div>
                        <table class="table table-borderless mt-4">
                            <tbody>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            <span class="fs-3 me-2" data-feather="user"
                                                style="
                                                                                            height: 16px;
                                                                                            width: 16px;
                                                                                        ">
                                            </span>
                                            <h5 class="lh-sm me-4">
                                                Nom
                                            </h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">
                                        :
                                    </td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-sm fw-normal text-body-secondary">
                                            {{ $user->full_name }}
                                        </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            <span class="fs-3 me-2" data-feather="home"
                                                style="
                                                                                            height: 16px;
                                                                                            width: 16px;
                                                                                        ">
                                            </span>
                                            <h5 class="lh-sm me-4">
                                                Adresse
                                            </h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">
                                        :
                                    </td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-lg fw-normal text-body-secondary">
                                            {{ $user->adresse ?? 'Bamako' }}
                                        </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            <span class="fs-3 me-2" data-feather="phone"
                                                style="
                                                                                            height: 16px;
                                                                                            width: 16px;
                                                                                        ">
                                            </span>
                                            <h5 class="lh-sm me-4">
                                                Téléphone
                                            </h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">
                                        :
                                    </td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-sm fw-normal text-body-secondary">
                                            {{ $user->tel ?? '+223 XXXXXXXX' }}
                                        </h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr class="my-6" />
                        @if ($commande->statut === 'en_attente')
                            <h3 class="mb-5">Type de livraison</h3>
                            {{-- Remplacez votre HTML de livraison statique --}}
                            <livewire:frontend.checkout.shipping-options :commande="$commande" />
                            <hr class="my-6" />
                            <h3 class="mb-5">Méthode de paiement</h3>
                            <div class="row g-4 mb-7">
                                {{-- Paiement (NOUVEAU) --}}
                                <livewire:frontend.checkout.payment-options :commande="$commande" />
                            </div>
                            {{-- ✅ BOUTONS SÉPARÉS --}}
                            <livewire:frontend.checkout.checkout-actions :commande="$commande" />
                        @elseif ($commande->statut === 'paiement_declare')
                            <div class="alert alert-warning d-flex align-items-center">
                                <span class="fas fa-clock me-2"></span>
                                Votre paiement a été déclaré. Il est en cours de vérification par le vendeur.
                            </div>
                        @elseif (!$isCancelled)
                            <div class="alert alert-success d-flex align-items-center">
                                <span class="fas fa-check-circle me-2"></span>
                                Paiement confirmé — statut : <strong class="ms-1">{{ $commande->status_label }}</strong>.
                            </div>
                        @endif
                    </div>

                </div>
            </div>
            <!-- end of .container-->
        </section>
        <!-- <section> close ============================-->
        <!-- ============================================-->

        {{-- <div class="container py-4">
            <h3>Commande #{{ $commande->id }}</h3>

            <p>Montant total: <strong>{{ number_format($commande->total, 2, ',', ' ') }} CFA</strong></p>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @php
                $proof = \App\Models\PaymentProof::where('order_id', $commande->id)->latest()->first();
            @endphp

            @if ($proof)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="d-flex align-items-center gap-2">
                            Preuve envoyée
                            <span class="badge bg-{{ $proof->status_badge_class }}">{{ $proof->status_label }}</span>
                        </h5>
                        @if (is_array($proof->photos))
                            @foreach ($proof->photos as $p)
                                <img src="{{ asset('storage/' . $p) }}" alt="preuve" class="img-thumbnail me-2"
                                    style="max-width:200px;" />
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <h5>Envoyer preuve de paiement mobile</h5>
                    <form action="{{ route('paiement.mobile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $commande->id }}" />

                        <div class="mb-3">
                            <label class="form-label">Fournisseur</label>
                            <select name="provider" class="form-select" required>
                                <option value="orange">Orange Money</option>
                                <option value="malitel">Malitel</option>
                                <option value="wave">Wave</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Numéro (optionnel)</label>
                            <input type="text" name="phone" class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Photos de la transaction (reçu)</label>
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*" />
                        </div>

                        <button class="btn btn-primary">Envoyer la preuve</button>
                    </form>
                </div>
            </div>
        </div> --}}

        @include('partials.footer')

    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

@endsection
