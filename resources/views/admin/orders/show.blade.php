@extends('admin.layouts.app')

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
                                    'en_cours' => 'info',
                                    'expediee' => 'primary',
                                    'livree' => 'success',
                                    'annulee' => 'danger'
                                ][$order->status] ?? 'secondary';

                                $statusText = [
                                    'en_attente' => 'En attente',
                                    'en_cours' => 'En cours de traitement',
                                    'expediee' => 'Expédiée',
                                    'livree' => 'Livrée',
                                    'annulee' => 'Annulée'
                                ][$order->status] ?? $order->status;
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

                        @if($order->status !== 'annulee' && $order->status !== 'livree')
                            <div class="btn-group float-end">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Changer le statut
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach([
                                        'en_attente' => 'En attente',
                                        'en_cours' => 'En cours de traitement',
                                        'expediee' => 'Marquer comme expédiée',
                                        'livree' => 'Marquer comme livrée',
                                        'annulee' => 'Annuler la commande'
                                    ] as $status => $label)
                                        @if($order->status !== $status)
                                            <li>
                                                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ $status }}">
                                                    <button type="submit" class="dropdown-item"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir passer cette commande en statut {{ strtolower($label) }} ?')">
                                                        {{ $label }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            @if($order->paiement && ($order->paiement->status === 'en_attente' || $order->paiement->status === 'pending'))
                                <form action="{{ route('admin.orders.confirm-payment', $order) }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Confirmer le paiement et marquer la commande comme payée ?')">
                                        <i class="fas fa-check me-1"></i> Confirmer paiement
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
