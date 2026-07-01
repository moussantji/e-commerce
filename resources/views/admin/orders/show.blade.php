@extends('admin.base')

@section('title', 'Détails de la commande #' . $order->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Détails de la commande #{{ $order->id }}</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informations client</h6>
                            <address>
                                <strong>{{ $order->user->name ?? 'Client inconnu' }}</strong><br>
                                {{ $order->user->email ?? 'Email non disponible' }}<br>
                                Téléphone: {{ $order->user->tel ?? 'Non renseigné' }}
                            </address>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6>Statut de la commande</h6>
                            @php
                                $statusClass = [
                                    'en_attente' => 'warning',
                                    'paiement_declare' => 'warning',
                                    'payee' => 'primary',
                                    'traitement' => 'info',
                                    'en_cours' => 'info',
                                    'expediee' => 'primary',
                                    'expedie' => 'primary',
                                    'livree' => 'success',
                                    'livre' => 'success',
                                    'annulee' => 'danger',
                                    'annule' => 'danger'
                                ][$order->statut] ?? 'secondary';

                                $statusText = [
                                    'en_attente' => 'En attente de paiement',
                                    'paiement_declare' => 'Paiement en vérification',
                                    'payee' => 'Payée',
                                    'traitement' => 'En préparation',
                                    'en_cours' => 'En préparation',
                                    'expediee' => 'Expédiée',
                                    'expedie' => 'Expédiée',
                                    'livree' => 'Livrée',
                                    'livre' => 'Livrée',
                                    'annulee' => 'Annulée',
                                    'annule' => 'Annulée'
                                ][$order->statut] ?? $order->statut;
                            @endphp
                            <span class="badge bg-{{ $statusClass }} fs-6">{{ $statusText }}</span>

                            <div class="mt-2">
                                <small class="text-muted">
                                    Date de commande: {{ $order->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6>Produits commandés</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-end">Prix unitaire</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->produits as $produit)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($produit->image)
                                                    <img src="{{ asset('storage/' . $produit->image) }}"
                                                         alt="{{ $produit->nom }}"
                                                         class="img-thumbnail me-3"
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $produit->nom }}</h6>
                                                    <small class="text-muted">
                                                        Réf: {{ $produit->reference ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($produit->pivot->prix_unitaire, 2, ',', ' ') }} €
                                        </td>
                                        <td class="text-center">
                                            {{ $produit->pivot->quantity }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($produit->pivot->total_ligne, 2, ',', ' ') }} €
                                        </td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Sous-total</td>
                                    <td class="text-end">
                                        {{ number_format($order->produits->sum('pivot.total_ligne'), 2, ',', ' ') }} €
                                    </td>
                                </tr>
                                @if($order->frais_livraison > 0)
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Frais de livraison</td>
                                        <td class="text-end">
                                            {{ number_format($order->frais_livraison, 2, ',', ' ') }} €
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total TTC</td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($order->total, 2, ',', ' ') }} €
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6>Adresse de livraison</h6>
                            <address>
                                {{ $order->adresse_livraison ?? 'Non spécifiée' }}
                            </address>
                        </div>
                        <div class="col-md-6">
                            <h6>Informations de facturation</h6>
                            <address>
                                {{ $order->adresse_facturation ?? 'Identique à l\'adresse de livraison' }}
                            </address>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i> Retour à la liste
                        </a>
                        @if($order->paiement)
                            <div class="float-start ms-3">
                                <strong>Preuve de paiement:</strong>
                                <div class="mt-2">
                                    @foreach($order->paiement->photos as $photo)
                                        <img src="{{ asset('storage/' . $photo->filename) }}" alt="preuve" class="img-thumbnail me-2" style="max-width:120px" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Workflow de traitement de la commande --}}
                    @php
                        $st = $order->statut;
                        $terminal = in_array($st, ['livre', 'livree', 'annule', 'annulee']);
                    @endphp
                    @unless($terminal)
                        <div class="card mt-4 border">
                            <div class="card-body">
                                <h6 class="mb-3">Traitement de la commande</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    {{-- Étape paiement : confirmer si pas encore payée --}}
                                    @if(in_array($st, ['en_attente', 'paiement_declare']))
                                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="payee">
                                            <button class="btn btn-success"
                                                onclick="return confirm('Confirmer le paiement et marquer la commande comme payée ?')">
                                                <i class="fas fa-check me-1"></i> Confirmer le paiement (Payée)
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Étape préparation --}}
                                    @if(in_array($st, ['payee']))
                                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="traitement">
                                            <button class="btn btn-info text-white">
                                                <i class="fas fa-box me-1"></i> Marquer « En préparation »
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Étape expédition --}}
                                    @if(in_array($st, ['payee', 'traitement']))
                                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="expedie">
                                            <button class="btn btn-primary">
                                                <i class="fas fa-truck me-1"></i> Marquer « Expédiée »
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Étape livraison --}}
                                    @if(in_array($st, ['traitement', 'expedie', 'expedition']))
                                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="livre">
                                            <button class="btn btn-success">
                                                <i class="fas fa-check-double me-1"></i> Marquer « Livrée »
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Annulation (toujours possible tant que non terminal) --}}
                                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="annule">
                                        <button class="btn btn-outline-danger"
                                            onclick="return confirm('Annuler cette commande ?')">
                                            <i class="fas fa-times me-1"></i> Annuler
                                        </button>
                                    </form>
                                </div>
                                <small class="text-muted d-block mt-3">
                                    Le client est notifié (email + push) à chaque changement de statut.
                                </small>
                            </div>
                        </div>
                    @endunless
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
