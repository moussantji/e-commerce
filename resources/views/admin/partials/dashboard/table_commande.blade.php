<table class="table table-sm fs-9 mb-0">
    <thead>
        <tr>
            <th class="white-space-nowrap fs-9 align-middle ps-0" style="width:26px;">
                <div class="form-check mb-0 fs-8"><input class="form-check-input" id="checkbox-bulk-order-select"
                        type="checkbox" data-bulk-select='{"body":"order-table-body"}' /></div>
            </th>
            <th class="sort white-space-nowrap align-middle pe-3" scope="col" data-sort="order" style="width:5%;">
                ID
            </th>
            <th class="sort align-middle ps-8" scope="col" data-sort="customer" style="width:28%; min-width: 250px;">
                CLIENT</th>
            <th class="sort align-middle text-start pe-3" scope="col" data-sort="fulfilment_status"
                style="width:12%; min-width: 200px;">METHODE DE PAIEMENT
            </th>
            <th class="sort align-middle pe-3" scope="col" data-sort="payment_status" style="width:10%;">METHODE DE
                LIVRAISON</th>
            <th class="sort align-middle pe-3" scope="col" data-sort="payment_status" style="width:10%;">STATUS DE
                PAIEMENT</th>

            <th class="sort align-middle text-end" scope="col" data-sort="total" style="width:6%;">TOTAL</th>
            <th class="sort align-middle text-end pe-0" scope="col" data-sort="date">DATE</th>
            <th class="align-middle text-end pe-0" scope="col" style="width:1%;">ACTIONS</th>
        </tr>
    </thead>
    <tbody class="list" id="order-table-body">
        @foreach ($commandes as $commande)
            @php
                $statusClass = $commande->status_badge_class;
                $statusIcon = $commande->status_icon;
                $statusLabel = $commande->status_label;
            @endphp
            <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                <td class="fs-9 align-middle px-0 py-3">
                    <div class="form-check mb-0 fs-8">
                        <input class="form-check-input" type="checkbox"
                            data-bulk-select-row='{"order":{{ $commande->id }},"total":{{ $commande->total }},"customer":{"avatar":"/team/32.webp","name":"{{ $commande->user ? $commande->user->name : 'Client inconnu' }}"},"payment_status":{"label":"{{ $statusLabel }}","type":"badge-phoenix-{{ $statusClass }}","icon":"{{ $statusIcon }}"},"fulfilment_status":{"label":"{{ $commande->livraison ? ($commande->livraison->carrier_name ?? 'Non spécifié') : 'Non spécifié' }}","type":"badge-phoenix-secondary","icon":"truck"},"delivery_type":"{{ $commande->paiement ? ($commande->paiement->method ?? 'Non spécifié') : 'Non spécifié' }}","date":"{{ $commande->created_at->format('M d, H:i') }}"}' />
                    </div>
                </td>
                <td class="order align-middle white-space-nowrap py-0"><a class="fw-semibold"
                        href="{{ route('admin.orders.show', $commande) }}">#{{ $commande->id }}</a>
                </td>
                <td class="customer align-middle white-space-nowrap ps-8"><a class="d-flex align-items-center text-body"
                        href="{{ route('admin.orders.show', $commande) }}">
                        <div class="avatar avatar-m"><img class="rounded-circle" src="../../../assets/img/team/32.webp"
                                alt="" /></div>
                        <h6 class="mb-0 ms-3 text-body">{{ $commande->user->name ?? 'Client inconnu' }}</h6>
                    </a></td>
                <td class="delivery_type align-middle white-space-nowrap text-body fs-9 text-start">
                    @if($commande->paiement)
                        {{ $commande->paiement->method_name ?? 'Méthode non spécifiée' }}
                    @else
                        <span class="text-muted">Non spécifié</span>
                    @endif
                </td>

                <td class="fulfilment_status align-middle white-space-nowrap text-start fw-bold">
                    @if($commande->livraison)
                        <span class="badge badge-phoenix fs-10 badge-phoenix-primary">
                            <span class="badge-label">{{ $commande->livraison->method_name ?? 'Transport non spécifié' }}</span>
                            <span class="ms-1" data-feather="truck" style="height:12.8px;width:12.8px;"></span>
                        </span>
                    @else
                        <span class="text-muted">Non spécifié</span>
                    @endif
                </td>
                @php
                    $statusClass = $commande->status_badge_class;
                    $statusIcon = $commande->status_icon;
                    $statusLabel = $commande->status_label;
                @endphp

                <td class="payment_status align-middle white-space-nowrap text-start fw-bold">
                    <span class="badge badge-phoenix fs-10 badge-phoenix-{{ $statusClass }}">
                        <span class="badge-label">{{ $statusLabel }}</span>
                        <span class="ms-1" data-feather="{{ $statusIcon }}"
                            style="height:12.8px;width:12.8px;"></span>
                    </span>
                </td>



                <td class="total align-middle text-end fw-semibold text-body-highlight">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</td>
                <td class="date align-middle white-space-nowrap text-body-tertiary fs-9 ps-4 text-end">{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                <td class="align-middle text-end pe-0 white-space-nowrap">
                    @php
                        $terminal = \App\Support\OrderStatus::isTerminal($commande->statut);
                    @endphp
                    <div class="btn-group">
                        <a href="{{ route('admin.orders.show', $commande) }}"
                            class="btn btn-sm btn-phoenix-secondary" title="Voir / Éditer">
                            <span class="fas fa-eye"></span>
                        </a>
                        @unless($terminal)
                            <form action="{{ route('admin.orders.update-status', $commande) }}" method="POST"
                                onsubmit="return confirm('Annuler cette commande ?');">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="annule">
                                <button type="submit" class="btn btn-sm btn-phoenix-danger" title="Annuler la commande">
                                    <span class="fas fa-times"></span>
                                </button>
                            </form>
                        @endunless
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
