<div>
    <div class="scrollbar">
        <ul class="nav nav-underline fs-9 flex-nowrap mb-3 pb-1" id="myTab" role="tablist">
            <li class="nav-item me-3"><a class="nav-link text-nowrap active" id="orders-tab" data-bs-toggle="tab"
                    href="#tab-orders" role="tab" aria-controls="tab-orders" aria-selected="true"><span
                        class="fas fa-shopping-cart me-2"></span>Orders <span class="text-body-tertiary fw-normal">
                        ({{ $commandes->count() }})</span></a></li>
            <li class="nav-item me-3"><a class="nav-link text-nowrap" id="wishlist-tab" data-bs-toggle="tab"
                    href="#tab-wishlist" role="tab" aria-controls="tab-orders" aria-selected="true"><span
                        class="fas fa-heart me-2"></span>Wishlist <span class="text-body-tertiary fw-normal">
                        ({{ $wishlist->count() }})</span></a></li>
            <li class="nav-item"><a class="nav-link text-nowrap" id="personal-info-tab" data-bs-toggle="tab"
                    href="#tab-personal-info" role="tab" aria-controls="tab-personal-info"
                    aria-selected="true"><span class="fas fa-user me-2"></span>Personal info</a></li>
            <li class="nav-item">
                <a class="nav-link text-nowrap" id="password-tab" data-bs-toggle="tab" href="#tab-password"
                    role="tab" aria-controls="tab-password" aria-selected="false">
                    <span class="fas fa-key me-2"></span>Mot de passe
                </a>
            </li>
        </ul>
    </div>
    <div class="tab-content" id="profileTabContent">
        <div class="tab-pane fade show active" id="tab-orders" role="tabpanel" aria-labelledby="orders-tab">
            <div class="border-top border-bottom border-translucent" id="profileOrdersTable"
                data-list='{"valueNames":["order","status","delivery","date","total"],"page":6,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table class="table fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="sort white-space-nowrap align-middle pe-3 ps-0" scope="col"
                                    data-sort="order" style="width:15%; min-width:140px">ORDER</th>
                                <th class="sort align-middle pe-3" scope="col" data-sort="status"
                                    style="width:15%; min-width:180px">STATUS</th>
                                <th class="sort align-middle text-start" scope="col" data-sort="delivery"
                                    style="width:20%; min-width:160px">DELIVERY
                                    METHOD</th>
                                <th class="sort align-middle pe-0 text-end" scope="col" data-sort="date"
                                    style="width:15%; min-width:160px">DATE</th>
                                <th class="sort align-middle text-end" scope="col" data-sort="total"
                                    style="width:15%; min-width:160px">TOTAL</th>
                                <th class="align-middle pe-0 text-end" scope="col" style="width:15%;">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="list" id="profile-order-table-body">
                            @forelse ($commandes as $commande)
                                @php
                                    // Status configuration mapping
                                    $statusConfig = [
                                        'en_attente' => ['badge-phoenix-secondary', 'clock', 'Pending'],
                                        'traitement' => ['badge-phoenix-info', 'clock', 'Processing'],
                                        'expedition' => ['badge-phoenix-success', 'truck', 'Shipped'],
                                        'livree' => ['badge-phoenix-success', 'check', 'Delivered'],
                                        'annulee' => ['badge-phoenix-danger', 'x', 'Cancelled'],
                                        'partiellement_livree' => [
                                            'badge-phoenix-warning',
                                            'clock',
                                            'Partially Delivered',
                                        ],
                                    ];

                                    $status = $statusConfig[$commande->statut] ?? [
                                        'badge-phoenix-secondary',
                                        'help-circle',
                                        'Unknown',
                                    ];
                                    $isCancelled = $commande->statut === 'annulee';
                                @endphp

                                <tr
                                    class="hover-actions-trigger btn-reveal-trigger position-static {{ $isCancelled ? 'opacity-50' : '' }}">
                                    <!-- Order Number -->
                                    <td class="order align-middle white-space-nowrap py-2 ps-0">
                                        <a class="fw-semibold {{ $isCancelled ? 'text-body-tertiary text-opacity-85 text-decoration-none pointer-events-none' : 'text-primary' }}"
                                            href="{{ route('commande.show', $commande->id) }}">
                                            #{{ $commande->numero_commande ?? $commande->id }}
                                        </a>
                                    </td>

                                    <!-- Status -->
                                    <td
                                        class="status align-middle white-space-nowrap text-start fw-bold text-body-tertiary py-2">
                                        <span class="badge badge-phoenix fs-10 {{ $status[0] }}">
                                            <span class="badge-label">{{ $status[2] }}</span>
                                            <span class="ms-1" data-feather="{{ $status[1] }}"
                                                style="height:12.8px;width:12.8px;"></span>
                                        </span>
                                    </td>

                                    <!-- Delivery Method -->
                                    <td class="delivery align-middle white-space-nowrap text-body py-2">
                                        {{ $commande->livraison->nom ?? 'Standard livraison' }}
                                    </td>

                                    <!-- Order Date -->
                                    <td class="total align-middle text-body-tertiary text-end py-2">
                                        {{ $commande->date_commande?->format('M j, g:i A') ?? $commande->created_at->format('M j, g:i A') }}
                                    </td>

                                    <!-- Total -->
                                    <td
                                        class="date align-middle fw-semibold text-end py-2 {{ $isCancelled ? 'text-body-tertiary text-opacity-85' : 'text-body-highlight' }}">
                                        ${{ number_format($commande->total, 0) }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="align-middle text-end white-space-nowrap pe-0 action py-2">
                                        <div class="btn-reveal-trigger position-static">
                                            <button
                                                class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal"
                                                type="button" data-bs-toggle="dropdown" data-boundary="window"
                                                aria-haspopup="true" aria-expanded="false" data-bs-reference="parent">
                                                <span class="fas fa-ellipsis-h fs-10"></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end py-2">
                                                <a class="dropdown-item"
                                                    href="{{ route('commande.show', $commande->id) }}">View</a>
                                                @if ($commande->statut === 'payee' || $commande->statut === 'paid' || $commande->statut === 'payé')
                                                    <a class="dropdown-item"
                                                        href="{{ route('commande.pdf', $commande->id) }}">Export</a>
                                                @endif

                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('commande.destroy', $commande->id) }}"
                                                    method="POST" class="d-inline"
                                                    id="delete-form-{{ $commande->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <a class="dropdown-item text-danger" href=""
                                                    onclick="event.preventDefault();
                                                        document.getElementById('delete-form-{{ $commande->id }}').submit();
                                                    ">
                                                    Remove
                                                </a>

                                            </div>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-body-tertiary">
                                        Aucune commande trouvée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
                <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                    <div class="col-auto d-flex">
                        <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info">
                        </p><a class="fw-semibold" href="#!" data-list-view="*">View all<span
                                class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a
                            class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span
                                class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                    </div>
                    <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span
                                class="fas fa-chevron-left"></span></button>
                        <ul class="mb-0 pagination"></ul><button class="page-link pe-0"
                            data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-wishlist" role="tabpanel" aria-labelledby="wishlist-tab">
            <div class="border-y border-translucent" id="productWishlistTable"
                data-list='{"valueNames":["products","color","size","price","quantity","total"],"page":5,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table class="table fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="sort white-space-nowrap align-middle fs-10" scope="col"
                                    style="width:7%;"></th>
                                <th class="sort white-space-nowrap align-middle" scope="col"
                                    style="width:30%; min-width:250px;" data-sort="products">PRODUCTS
                                </th>
                                <th class="sort align-middle" scope="col" data-sort="color" style="width:16%;">
                                    COLOR</th>
                                <th class="sort align-middle" scope="col" data-sort="size" style="width:10%;">
                                    SIZE</th>
                                <th class="sort align-middle text-end" scope="col" data-sort="price"
                                    style="width:10%;">PRICE</th>
                                <th class="sort align-middle text-end pe-0" scope="col" style="width:35%;"> </th>
                            </tr>
                        </thead>
                        <tbody class="list" id="profile-wishlist-table-body">
                            @forelse ($wishlist as $item)
                                @php
                                    $couleur = $item->caracteristiques->firstWhere('name', 'Couleur');
                                    $taille = $item->caracteristiques->firstWhere('name', 'Taille');
                                @endphp

                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="align-middle white-space-nowrap ps-0 py-0">
                                        <a class="border border-translucent rounded-2 d-inline-block"
                                            href="{{ route('produits.show', ['slug' => $item->getSlug(), 'id' => $item->id]) }}">
                                            <img src="{{ $item->getPhoto() ? $item->getPhoto()->getImageUrl(60, 60) : 'assets/img/products/default.png' }}"
                                                alt="" width="53" />
                                        </a>
                                    </td>
                                    <td class="products align-middle pe-11">
                                        <a class="fw-semibold mb-0 line-clamp-1"
                                            href="{{ route('produits.show', ['slug' => $item->getSlug(), 'id' => $item->id]) }}">
                                            {{ $item->name }}
                                        </a>
                                    </td>
                                    <td class="color align-middle white-space-nowrap fs-9 text-body">
                                        {{ $couleur ? $couleur->pivot->value : 'N/A' }}
                                    </td>
                                    <td
                                        class="size align-middle white-space-nowrap text-body-tertiary fs-9 fw-semibold">
                                        {{ $taille ? $taille->pivot->value : 'N/A' }}
                                    </td>
                                    <td class="price align-middle text-body fs-9 fw-semibold text-end">
                                        {{ $item->formatted_price }}
                                    </td>
                                    <td
                                        class="total align-middle fw-bold text-body-highlight text-end text-nowrap pe-0">
                                        <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm text-body-quaternary text-body-tertiary-hover me-2">
                                                <span class="fas fa-trash"></span>
                                            </button>
                                        </form>

                                        {{-- Ajouter au panier en POST --}}
                                        <form action="{{ route('cart.add', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary fs-10">
                                                <span class="fas fa-shopping-cart me-1 fs-10"></span>
                                                Ajouter au panier
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-body-tertiary">
                                        Votre liste de souhaits est vide
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                    <div class="col-auto d-flex">
                        <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info">
                        </p><a class="fw-semibold" href="#!" data-list-view="*">View all<span
                                class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a
                            class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span
                                class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                    </div>
                    <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span
                                class="fas fa-chevron-left"></span></button>
                        <ul class="mb-0 pagination"></ul><button class="page-link pe-0"
                            data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade show" id="tab-personal-info" role="tabpanel" aria-labelledby="personal-info-tab">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
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
                        <small class="text-muted">Contactez l'administrateur pour modifier cette
                            information</small>
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
                                value="{{ $user->social_links ? (is_string($user->social_links) ? json_decode($user->social_links, true)['facebook'] ?? '' : $user->social_links['facebook'] ?? '') : '' }}">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light"><i class="fab fa-twitter text-info"></i></span>
                            <input type="url" class="form-control" id="twitter_url" name="twitter_url"
                                placeholder="https://twitter.com/votrepseudo"
                                value="{{ $user->social_links ? (is_string($user->social_links) ? json_decode($user->social_links, true)['twitter'] ?? '' : $user->social_links['twitter'] ?? '') : '' }}">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light"
                                style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: white;">
                                <i class="fab fa-instagram"></i>
                            </span>
                            <input type="url" class="form-control" id="instagram_url" name="instagram_url"
                                placeholder="https://instagram.com/votrepseudo"
                                value="{{ $user->social_links ? (is_string($user->social_links) ? json_decode($user->social_links, true)['instagram'] ?? '' : $user->social_links['instagram'] ?? '') : '' }}">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light"><i
                                    class="fab fa-linkedin-in text-primary"></i></span>
                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url"
                                placeholder="https://linkedin.com/in/votrepseudo"
                                value="{{ $user->social_links ? (is_string($user->social_links) ? json_decode($user->social_links, true)['linkedin'] ?? '' : $user->social_links['linkedin'] ?? '') : '' }}">
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
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row gx-3 gy-4 mb-5">

                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-body-highlight fs-8 ps-0 text-capitalize lh-sm"
                                for="new_password">Nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    required minlength="8">
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
